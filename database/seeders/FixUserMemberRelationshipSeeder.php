<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;

class FixUserMemberRelationshipSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $members = Member::all();
        
        foreach ($members as $key => $member) {
            if ($key < $users->count()) {
                $member->update(['user_id' => $users[$key]->id]);
            }
        }
        
        echo "Fixed user-member relationships\n";
    }
}