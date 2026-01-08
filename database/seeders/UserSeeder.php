<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@bss.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true
            ],
            [
                'name' => 'BSS Manager',
                'email' => 'manager@bss.com',
                'password' => Hash::make('manager123'),
                'role' => 'manager',
                'is_active' => true
            ],
            [
                'name' => 'BSS Treasurer',
                'email' => 'treasurer@bss.com',
                'password' => Hash::make('treasurer123'),
                'role' => 'treasurer',
                'is_active' => true
            ],
            [
                'name' => 'BSS Secretary',
                'email' => 'secretary@bss.com',
                'password' => Hash::make('secretary123'),
                'role' => 'secretary',
                'is_active' => true
            ],
            [
                'name' => 'John Member',
                'email' => 'member@bss.com',
                'password' => Hash::make('member123'),
                'role' => 'member',
                'is_active' => true
            ]
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}