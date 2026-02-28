<?php

namespace App\Observers;

use App\Models\Member;
use App\Models\User;
use App\Services\UserMemberSyncService;
use Illuminate\Support\Facades\DB;

class UserObserver
{
    private static bool $syncing = false;

    public function created(User $user): void
    {
        $this->syncUserToMember($user);
    }

    public function updated(User $user): void
    {
        $this->syncUserToMember($user);
    }

    private function syncUserToMember(User $user): void
    {
        if (self::$syncing) {
            return;
        }

        self::$syncing = true;
        try {
            app(UserMemberSyncService::class)->syncFromUser($user);
        } finally {
            self::$syncing = false;
        }
    }

    public function deleted(User $user): void
    {
        if (self::$syncing) {
            return;
        }

        self::$syncing = true;
        try {
            $member = Member::withTrashed()
                ->where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();

            if (!$member) {
                return;
            }

            DB::table('loans')->where('member_id', $member->member_id)->delete();
            DB::table('transactions')->where('member_id', $member->member_id)->delete();
            DB::table('savings_history')->where('member_id', $member->member_id)->delete();

            $member->forceDeleteQuietly();
        } finally {
            self::$syncing = false;
        }
    }
}
