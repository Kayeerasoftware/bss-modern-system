<?php

namespace App\Observers;

use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberObserver
{
    private static bool $syncing = false;

    public function created(Member $member): void
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
                $user = User::withoutEvents(function () use ($member) {
                    return User::create([
                        'name' => $member->full_name,
                        'email' => $member->email,
                        'password' => $member->password ?: Hash::make(Str::random(20)),
                        'role' => $member->role ?: 'client',
                        'status' => $member->status ?: 'active',
                        'is_active' => ($member->status ?? 'active') === 'active',
                        'phone' => $member->contact,
                        'location' => $member->location,
                        'profile_picture' => $member->profile_picture,
                    ]);
                });
            } else {
                $user->fill([
                    'name' => $member->full_name,
                    'email' => $member->email,
                    'role' => $member->role ?: $user->role,
                    'status' => $member->status ?: ($user->is_active ? 'active' : 'inactive'),
                    'is_active' => ($member->status ?? 'active') === 'active',
                    'phone' => $member->contact,
                    'location' => $member->location,
                    'profile_picture' => $member->profile_picture,
                ]);

                if (!empty($member->password)) {
                    $user->password = $member->password;
                }

                if ($user->isDirty()) {
                    $user->saveQuietly();
                }
            }

            if ((int) $member->user_id !== (int) $user->id) {
                $member->user_id = $user->id;
                $member->saveQuietly();
            }
        } finally {
            self::$syncing = false;
        }
    }

    public function updated(Member $member): void
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
                $user = User::withoutEvents(function () use ($member) {
                    return User::create([
                        'name' => $member->full_name,
                        'email' => $member->email,
                        'password' => $member->password ?: Hash::make(Str::random(20)),
                        'role' => $member->role ?: 'client',
                        'status' => $member->status ?: 'active',
                        'is_active' => ($member->status ?? 'active') === 'active',
                        'phone' => $member->contact,
                        'location' => $member->location,
                        'profile_picture' => $member->profile_picture,
                    ]);
                });
            } else {
                $user->fill([
                    'name' => $member->full_name,
                    'email' => $member->email,
                    'role' => $member->role ?: $user->role,
                    'status' => $member->status ?: ($user->is_active ? 'active' : 'inactive'),
                    'is_active' => ($member->status ?? 'active') === 'active',
                    'phone' => $member->contact,
                    'location' => $member->location,
                    'profile_picture' => $member->profile_picture,
                ]);

                if ($member->wasChanged('password') && !empty($member->password)) {
                    $user->password = $member->password;
                }

                if ($user->isDirty()) {
                    $user->saveQuietly();
                }
            }

            if ((int) $member->user_id !== (int) $user->id) {
                $member->user_id = $user->id;
                $member->saveQuietly();
            }
        } finally {
            self::$syncing = false;
        }
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
                    'is_active' => false,
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
                'is_active' => true,
                'status' => 'active',
            ]);
        } finally {
            self::$syncing = false;
        }
    }
}
