<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'ceo', 'td', 'cashier', 'shareholder', 'client'];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role],
                [
                    'name'        => $role,
                    'description' => ucfirst($role) . ' role',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );
        }
    }
}
