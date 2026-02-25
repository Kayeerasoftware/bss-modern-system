<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class RoleStatusSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'client' => 'Client/Member',
            'shareholder' => 'Shareholder',
            'cashier' => 'Cashier',
            'td' => 'Technical Director',
            'ceo' => 'CEO',
            'admin' => 'Administrator'
        ];

        foreach ($roles as $roleKey => $roleLabel) {
            Setting::updateOrCreate(
                ['key' => "role_status_{$roleKey}"],
                ['value' => '1']
            );
        }

        $this->command->info('Role status settings initialized successfully!');
    }
}
