<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\User;

class SyncMembersUsers extends Command
{
    protected $signature = 'sync:members-users';
    protected $description = 'Sync members and users tables';

    public function handle()
    {
        $this->info('Syncing members and users...');

        // Create users for members without user_id
        $membersWithoutUsers = Member::whereNull('user_id')->get();
        foreach ($membersWithoutUsers as $member) {
            $user = User::where('email', $member->email)->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $member->full_name,
                    'email' => $member->email,
                    'password' => $member->password,
                    'role' => $member->role,
                    'phone' => $member->contact,
                    'location' => $member->location,
                    'profile_picture' => $member->profile_picture,
                    'is_active' => true,
                ]);
                $this->info("Created user for member: {$member->full_name}");
            }
            
            $member->update(['user_id' => $user->id]);
        }

        // Create members for users without member
        $usersWithoutMembers = User::doesntHave('member')->get();
        foreach ($usersWithoutMembers as $user) {
            $lastMember = Member::withTrashed()->orderBy('id', 'desc')->first();
            $nextId = $lastMember ? $lastMember->id + 1 : 1;
            
            Member::create([
                'member_id' => 'BSS' . str_pad($nextId, 4, '0', STR_PAD_LEFT),
                'full_name' => $user->name,
                'email' => $user->email,
                'contact' => $user->phone ?? '',
                'location' => $user->location ?? '',
                'occupation' => '',
                'password' => $user->password,
                'role' => $user->role,
                'profile_picture' => $user->profile_picture,
                'user_id' => $user->id,
            ]);
            $this->info("Created member for user: {$user->name}");
        }

        // Reconcile linked pairs (use most recently updated record as source of truth)
        $reconciled = 0;
        Member::with('user')->get()->each(function (Member $member) use (&$reconciled) {
            if (!$member->user) {
                return;
            }

            $user = $member->user;
            $memberIsNewer = ($member->updated_at?->timestamp ?? 0) >= ($user->updated_at?->timestamp ?? 0);

            if ($memberIsNewer) {
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
                    $reconciled++;
                }
                return;
            }

            $member->fill([
                'full_name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?: $member->role,
                'status' => $user->is_active ? 'active' : 'inactive',
                'contact' => $user->phone,
                'location' => $user->location,
                'profile_picture' => $user->profile_picture,
            ]);
            if (!empty($user->password)) {
                $member->password = $user->password;
            }
            if ((int) $member->user_id !== (int) $user->id) {
                $member->user_id = $user->id;
            }
            if ($member->isDirty()) {
                $member->saveQuietly();
                $reconciled++;
            }
        });

        $this->info('Sync completed!');
        $this->info('Total Members: ' . Member::count());
        $this->info('Total Users: ' . User::count());
        $this->info('Reconciled linked records: ' . $reconciled);
    }
}
