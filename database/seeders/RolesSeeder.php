<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin',       'display_name' => 'Administrator',      'priority' => 1],
            ['name' => 'ceo',         'display_name' => 'Chief Executive Officer', 'priority' => 2],
            ['name' => 'td',          'display_name' => 'Technical Director',  'priority' => 3],
            ['name' => 'cashier',     'display_name' => 'Cashier',             'priority' => 4],
            ['name' => 'shareholder', 'display_name' => 'Shareholder',         'priority' => 5],
            ['name' => 'client',      'display_name' => 'Client',              'priority' => 6],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                [
                    'name'         => $role['name'],
                    'display_name' => $role['display_name'],
                    'description'  => $role['display_name'] . ' role',
                    'priority'     => $role['priority'],
                    'is_active'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]
            );
        }
    }
}
