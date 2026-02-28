<?php

namespace App\Services;

use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserMemberSyncService
{
    private const HEALTHY_UNTIL_CACHE_KEY = 'user_member_sync:healthy_until';
    private const LOCK_CACHE_KEY = 'user_member_sync:lock';

    private ?int $nextMemberSequence = null;

    public function reconcileIfNeeded(): void
    {
        $healthyUntil = Cache::get(self::HEALTHY_UNTIL_CACHE_KEY);
        if ($healthyUntil && now()->lt($healthyUntil)) {
            return;
        }

        if (!$this->hasMismatches()) {
            Cache::put(self::HEALTHY_UNTIL_CACHE_KEY, now()->addMinutes(5), now()->addMinutes(5));
            return;
        }

        if (!Cache::add(self::LOCK_CACHE_KEY, true, now()->addSeconds(30))) {
            return;
        }

        try {
            $this->reconcileMissingLinks();
            Cache::put(self::HEALTHY_UNTIL_CACHE_KEY, now()->addMinutes(5), now()->addMinutes(5));
        } finally {
            Cache::forget(self::LOCK_CACHE_KEY);
        }
    }

    public function reconcileMissingLinks(): array
    {
        $report = [
            'users_checked' => 0,
            'members_checked' => 0,
            'members_created' => 0,
            'members_updated' => 0,
            'members_restored' => 0,
            'users_created' => 0,
            'users_updated' => 0,
            'members_linked' => 0,
        ];

        $usersWithoutMember = User::query()
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('members')
                    ->whereColumn('members.user_id', 'users.id');
            })
            ->get();

        foreach ($usersWithoutMember as $user) {
            $report['users_checked']++;
            $result = $this->syncFromUser($user);

            if ($result === 'created') {
                $report['members_created']++;
            } elseif ($result === 'updated') {
                $report['members_updated']++;
            } elseif ($result === 'restored') {
                $report['members_restored']++;
            } elseif ($result === 'linked') {
                $report['members_linked']++;
            }
        }

        $membersWithoutUser = Member::withTrashed()
            ->where(function ($query): void {
                $query->whereNull('user_id')
                    ->orWhereNotExists(function ($subQuery): void {
                        $subQuery->selectRaw('1')
                            ->from('users')
                            ->whereColumn('users.id', 'members.user_id');
                    });
            })
            ->get();

        foreach ($membersWithoutUser as $member) {
            $report['members_checked']++;
            $result = $this->syncFromMember($member);

            if ($result === 'created') {
                $report['users_created']++;
            } elseif ($result === 'updated') {
                $report['users_updated']++;
            } elseif ($result === 'linked') {
                $report['members_linked']++;
            }
        }

        return $report;
    }

    public function syncFromUser(User $user): string
    {
        $memberQuery = Member::withTrashed()->where('user_id', $user->id);
        if (!empty($user->email)) {
            $memberQuery->orWhere('email', $user->email);
        }

        $member = $memberQuery
            ->orderByRaw('CASE WHEN user_id = ? THEN 0 ELSE 1 END', [$user->id])
            ->first();

        if (!$member) {
            Member::withoutEvents(function () use ($user): void {
                Member::create([
                    'member_id' => $this->nextMemberId(),
                    'user_id' => $user->id,
                    'full_name' => $user->name,
                    'email' => $user->email,
                    'contact' => $user->phone ?? '',
                    'location' => $user->location ?? '',
                    'occupation' => '',
                    'password' => $user->password ?: Hash::make(Str::random(24)),
                    'role' => $user->role ?: 'client',
                    'status' => $user->is_active ? 'active' : 'inactive',
                    'profile_picture' => $user->profile_picture,
                    'savings' => 0,
                    'loan' => 0,
                    'savings_balance' => 0,
                    'balance' => 0,
                ]);
            });

            return 'created';
        }

        $wasTrashed = method_exists($member, 'trashed') && $member->trashed();
        if ($wasTrashed) {
            if (method_exists($member, 'restoreQuietly')) {
                $member->restoreQuietly();
            } else {
                $member->restore();
            }
        }

        $member->fill([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'email' => $user->email,
            'contact' => $user->phone,
            'location' => $user->location,
            'role' => $user->role ?: $member->role,
            'status' => $user->is_active ? 'active' : 'inactive',
            'profile_picture' => $user->profile_picture,
        ]);

        if (!empty($user->password) && ($user->wasChanged('password') || empty($member->password))) {
            $member->password = $user->password;
        }

        $wasDirty = $member->isDirty();
        if ($wasDirty) {
            $member->saveQuietly();
        }

        if ($wasTrashed) {
            return 'restored';
        }

        if ($wasDirty) {
            return 'updated';
        }

        return 'unchanged';
    }

    public function syncFromMember(Member $member): string
    {
        $user = null;

        if (!empty($member->user_id)) {
            $user = User::find($member->user_id);
        }

        if (!$user && !empty($member->email)) {
            $user = User::where('email', $member->email)->first();
        }

        $userStatus = strtolower((string) ($member->status ?? 'active'));
        $isActive = $userStatus !== 'inactive';

        if (!$user) {
            $user = User::withoutEvents(function () use ($member, $isActive) {
                return User::create([
                    'name' => $member->full_name ?: 'Member User',
                    'email' => $member->email,
                    'password' => $member->password ?: Hash::make(Str::random(24)),
                    'role' => $member->role ?: 'client',
                    'status' => $isActive ? 'active' : 'inactive',
                    'is_active' => $isActive,
                    'phone' => $member->contact,
                    'location' => $member->location,
                    'profile_picture' => $member->profile_picture,
                ]);
            });

            if ((int) $member->user_id !== (int) $user->id) {
                $member->user_id = $user->id;
                $member->saveQuietly();
            }

            return 'created';
        }

        $user->fill([
            'name' => $member->full_name ?: $user->name,
            'email' => $member->email ?: $user->email,
            'role' => $member->role ?: $user->role,
            'status' => $isActive ? 'active' : 'inactive',
            'is_active' => $isActive,
            'phone' => $member->contact,
            'location' => $member->location,
            'profile_picture' => $member->profile_picture,
        ]);

        if (!empty($member->password) && $member->password !== $user->password) {
            $user->password = $member->password;
        }

        $userWasDirty = $user->isDirty();
        if ($userWasDirty) {
            $user->saveQuietly();
        }

        $linked = false;
        if ((int) $member->user_id !== (int) $user->id) {
            $member->user_id = $user->id;
            $member->saveQuietly();
            $linked = true;
        }

        if ($userWasDirty) {
            return 'updated';
        }

        return $linked ? 'linked' : 'unchanged';
    }

    private function hasMismatches(): bool
    {
        $usersWithoutMember = User::query()
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('members')
                    ->whereColumn('members.user_id', 'users.id');
            })
            ->exists();

        if ($usersWithoutMember) {
            return true;
        }

        return Member::withTrashed()
            ->where(function ($query): void {
                $query->whereNull('user_id')
                    ->orWhereNotExists(function ($subQuery): void {
                        $subQuery->selectRaw('1')
                            ->from('users')
                            ->whereColumn('users.id', 'members.user_id');
                    });
            })
            ->exists();
    }

    private function nextMemberId(): string
    {
        if ($this->nextMemberSequence === null) {
            $lastMemberId = Member::withTrashed()
                ->where('member_id', 'like', 'BSS-C15-%')
                ->orderBy('member_id', 'desc')
                ->value('member_id');

            $next = 1;
            if (is_string($lastMemberId) && preg_match('/BSS-C15-(\d+)/', $lastMemberId, $matches)) {
                $next = ((int) $matches[1]) + 1;
            }

            $this->nextMemberSequence = $next;
        }

        $memberId = 'BSS-C15-' . str_pad((string) $this->nextMemberSequence, 4, '0', STR_PAD_LEFT);
        $this->nextMemberSequence++;

        return $memberId;
    }
}
