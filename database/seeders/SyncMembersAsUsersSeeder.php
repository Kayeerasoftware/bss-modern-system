<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class SyncMembersAsUsersSeeder extends Seeder
{
    public function run()
    {
        $members = Member::all();
        
        foreach ($members as $member) {
            // Create or update user for each member
            $user = User::updateOrCreate(
                ['email' => $member->email],
                [
                    'name' => $member->full_name,
                    'email' => $member->email,
                    'password' => Hash::make('password123'),
                    'role' => $member->role,
                    'is_active' => true,
                    'phone' => $member->contact,
                    'location' => $member->location,
                    'profile_picture' => $member->profile_picture,
                ]
            );
            
            // Update member with user_id
            $member->update(['user_id' => $user->id]);
        }
        
        echo "Synced " . $members->count() . " members as users\n";
    }
}