<?php

namespace App\Observers;

use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserObserver
{
    private static bool $syncing = false;

    public function updated(User $user): void
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

            if ((int) $member->user_id !== (int) $user->id) {
                $member->user_id = $user->id;
            }

            $member->fill([
                'full_name' => $user->name,
                'email' => $user->email,
                'contact' => $user->phone,
                'location' => $user->location,
                'role' => $user->role,
                'profile_picture' => $user->profile_picture,
                'status' => $user->is_active ? 'active' : 'inactive',
            ]);

            if ($user->wasChanged('password') && !empty($user->password)) {
                $member->password = $user->password;
            }

            if ($member->isDirty()) {
                $member->saveQuietly();
            }
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
