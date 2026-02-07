<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LinkMembersUsers extends Command
{
    protected $signature = 'members:link-users';
    protected $description = 'Link existing members to user accounts';

    public function handle()
    {
        $this->info('Linking members to users...');
        
        $members = Member::whereNull('user_id')->get();
        $linked = 0;
        $created = 0;
        
        foreach ($members as $member) {
            $user = User::where('email', $member->email)->first();
            
            if ($user) {
                $member->user_id = $user->id;
                $member->save();
                $this->info("Linked {$member->full_name} to existing user");
                $linked++;
            } else {
                $user = User::create([
                    'name' => $member->full_name,
                    'email' => $member->email,
                    'password' => Hash::make('password123'),
                    'role' => $member->role,
                    'is_active' => true,
                    'profile_picture' => $member->profile_picture
                ]);
                $member->user_id = $user->id;
                $member->save();
                $this->info("Created user for {$member->full_name}");
                $created++;
            }
        }
        
        $this->info("\nDone! Linked: {$linked}, Created: {$created}");
        return 0;
    }
}
