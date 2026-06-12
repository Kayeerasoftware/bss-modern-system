<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Member;

class SyncProfilePictures extends Command
{
    protected $signature = 'sync:profile-pictures';
    protected $description = 'Sync profile pictures between users and members';

    public function handle()
    {
        $this->info('Syncing profile pictures...');
        
        // Sync from members to users
        $members = Member::whereNotNull('profile_picture')->with('user')->get();
        foreach ($members as $member) {
            if ($member->user && !$member->user->profile_picture) {
                $member->user->update(['profile_picture' => $member->profile_picture]);
                $this->info("Synced picture from member {$member->member_id} to user {$member->user->email}");
            }
        }
        
        // Sync from users to members
        $users = User::whereNotNull('profile_picture')->with('member')->get();
        foreach ($users as $user) {
            if ($user->member && !$user->member->profile_picture) {
                $user->member->update(['profile_picture' => $user->profile_picture]);
                $this->info("Synced picture from user {$user->email} to member {$user->member->member_id}");
            }
        }
        
        $this->info('Profile pictures synced successfully!');
        return 0;
    }
}
