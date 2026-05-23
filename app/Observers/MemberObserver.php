<?php

namespace App\Observers;

use App\Models\Member;
use App\Models\User;

class MemberObserver
{
    private static bool $syncing = false;

    public function created(Member $member): void
    {
        $this->syncMemberToUser($member);
    }

    public function updated(Member $member): void
    {
        $this->syncMemberToUser($member);
    }

    public function deleted(Member $member): void
    {
        if (self::$syncing) {
            return;
        }

        self::$syncing = true;
        try {
            $user = null;
            if (!empty($member->user_id)) {
                $user = User::find($member->user_id);
            }
            if (!$user && !empty($member->email)) {
                $user = User::where('email', $member->email)->first();
            }
            if (!$user) {
                return;
            }

            if (method_exists($member, 'isForceDeleting') && $member->isForceDeleting()) {
                User::withoutEvents(function () use ($user) {
                    $user->delete();
                });
                return;
            }

            if ($user->is_active) {
                $user->updateQuietly([
                    'status' => 'inactive',
                ]);
            }
        } finally {
            self::$syncing = false;
        }
    }

    public function restored(Member $member): void
    {
        if (self::$syncing) {
            return;
        }

        self::$syncing = true;
        try {
            $user = null;
            if (!empty($member->user_id)) {
                $user = User::find($member->user_id);
            }
            if (!$user && !empty($member->email)) {
                $user = User::where('email', $member->email)->first();
            }
            if (!$user) {
                return;
            }

            $user->updateQuietly([
                'status' => 'active',
            ]);
        } finally {
            self::$syncing = false;
        }
    }

    private function syncMemberToUser(Member $member): void
    {
        if (self::$syncing) {
            return;
        }

        self::$syncing = true;
        try {
            if (empty($member->user_id)) {
                return;
            }

            $user = User::find($member->user_id);
            if (!$user) {
                return;
            }

            $user->fill([
                'name' => $member->full_name ?: $user->name,
                'email' => $member->email ?: $user->email,
                'status' => $member->membership_status ?: $user->status,
            ]);

            $primaryRole = $member->role;
            if ($primaryRole) {
                $user->role = $primaryRole;
            }

            if ($user->isDirty()) {
                $user->saveQuietly();
            }
        } finally {
            self::$syncing = false;
        }
    }
}
