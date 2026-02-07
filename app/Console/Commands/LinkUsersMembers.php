<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class LinkUsersMembers extends Command
{
    protected $signature = 'users:link-members';
    protected $description = 'Create member records for users without members';

    public function handle()
    {
        $this->info('Creating members for users...');
        
        $users = User::doesntHave('member')->get();
        $created = 0;
        
        foreach ($users as $user) {
            Member::create([
                'member_id' => 'BSS' . str_pad(Member::count() + 1, 3, '0', STR_PAD_LEFT),
                'full_name' => $user->name,
                'email' => $user->email,
                'location' => 'N/A',
                'occupation' => 'N/A',
                'contact' => 'N/A',
                'role' => $user->role,
                'savings' => 0,
                'loan' => 0,
                'savings_balance' => 0,
                'password' => $user->password,
                'profile_picture' => $user->profile_picture,
                'user_id' => $user->id
            ]);
            $this->info("Created member for {$user->name}");
            $created++;
        }
        
        $this->info("\nDone! Created: {$created}");
        return 0;
    }
}
