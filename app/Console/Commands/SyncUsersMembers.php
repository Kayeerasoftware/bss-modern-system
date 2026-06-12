<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Member;

class SyncUsersMembers extends Command
{
    protected $signature = 'sync:users-members';
    protected $description = 'Synchronize users and members data';

    public function handle()
    {
        // Force sync all member data to users
        Member::with('user')->get()->each(function ($member) {
            if ($member->user) {
                $member->user->updateQuietly([
                    'name' => $member->full_name,
                    'email' => $member->email,
                    'profile_picture' => $member->profile_picture,
                    'location' => $member->location,
                    'phone' => $member->contact,
                    'role' => $member->role,
                ]);
            }
        });

        $this->info('Members synced to users successfully.');
    }
}