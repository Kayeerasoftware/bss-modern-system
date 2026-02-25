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
                'name' => 'CEO User',
                'email' => 'ceo@bss.com',
                'password' => Hash::make('ceo123'),
                'role' => 'ceo',
                'is_active' => true
            ],
            [
                'name' => 'Technical Director',
                'email' => 'td@bss.com',
                'password' => Hash::make('td123'),
                'role' => 'td',
                'is_active' => true
            ],
            [
                'name' => 'Cashier User',
                'email' => 'cashier@bss.com',
                'password' => Hash::make('cashier123'),
                'role' => 'cashier',
                'is_active' => true
            ],
            [
                'name' => 'Shareholder User',
                'email' => 'shareholder@bss.com',
                'password' => Hash::make('shareholder123'),
                'role' => 'shareholder',
                'is_active' => true
            ],
            [
                'name' => 'Client User',
                'email' => 'client@bss.com',
                'password' => Hash::make('client123'),
                'role' => 'client',
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