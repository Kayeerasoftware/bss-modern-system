<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class UserMemberSeeder extends Seeder
{
    public function run()
    {
        // Skip if users already exist
        if (User::count() > 0) {
            echo "Users already exist, skipping seeding\n";
            return;
        }

        $userData = [
            ['name' => 'Admin User', 'email' => 'admin@bss.com', 'role' => 'admin', 'full_name' => 'Admin User', 'occupation' => 'Administrator', 'contact' => '+256700000001'],
            ['name' => 'John Doe', 'email' => 'john@bss.com', 'role' => 'client', 'full_name' => 'John Doe', 'occupation' => 'Farmer', 'contact' => '+256700000002'],
            ['name' => 'Jane Smith', 'email' => 'jane@bss.com', 'role' => 'cashier', 'full_name' => 'Jane Smith', 'occupation' => 'Cashier', 'contact' => '+256700000003'],
            ['name' => 'Mike Johnson', 'email' => 'mike@bss.com', 'role' => 'td', 'full_name' => 'Mike Johnson', 'occupation' => 'Technical Director', 'contact' => '+256700000004'],
            ['name' => 'Sarah Wilson', 'email' => 'sarah@bss.com', 'role' => 'ceo', 'full_name' => 'Sarah Wilson', 'occupation' => 'CEO', 'contact' => '+256700000005'],
            ['name' => 'David Brown', 'email' => 'david@bss.com', 'role' => 'shareholder', 'full_name' => 'David Brown', 'occupation' => 'Investor', 'contact' => '+256700000006'],
            ['name' => 'Mary Davis', 'email' => 'mary@bss.com', 'role' => 'client', 'full_name' => 'Mary Davis', 'occupation' => 'Teacher', 'contact' => '+256700000007'],
            ['name' => 'Peter Miller', 'email' => 'peter@bss.com', 'role' => 'client', 'full_name' => 'Peter Miller', 'occupation' => 'Trader', 'contact' => '+256700000008'],
        ];

        foreach ($userData as $index => $data) {
            // Create user first
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password123'),
                'role' => $data['role'],
                'is_active' => true,
                'phone' => $data['contact'],
                'location' => 'Kampala',
            ]);

            // Create member with user_id
            Member::create([
                'member_id' => 'BSS' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'occupation' => $data['occupation'],
                'contact' => $data['contact'],
                'password' => Hash::make('password123'),
                'role' => $data['role'],
                'savings' => rand(10000, 500000),
                'loan' => rand(0, 100000),
                'savings_balance' => rand(5000, 300000),
                'balance' => rand(1000, 50000),
                'location' => 'Kampala',
                'status' => 'active',
                'user_id' => $user->id,
            ]);
        }

        echo "Created " . count($userData) . " users and members with proper relationships\n";
    }
}