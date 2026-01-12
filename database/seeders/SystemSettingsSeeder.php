<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'interest_rate', 'value' => '5.5'],
            ['key' => 'min_savings', 'value' => '50000'],
            ['key' => 'max_loan', 'value' => '5000000'],
            ['key' => 'loan_fee', 'value' => '2.5'],
            ['key' => 'system_name', 'value' => 'BSS Investment Group'],
            ['key' => 'currency', 'value' => 'UGX'],
            ['key' => 'timezone', 'value' => 'Africa/Kampala'],
            ['key' => 'date_format', 'value' => 'Y-m-d'],
            ['key' => 'email_notifications', 'value' => 'true'],
            ['key' => 'sms_notifications', 'value' => 'false'],
            ['key' => 'loan_approval_notify', 'value' => 'true'],
            ['key' => 'transaction_notify', 'value' => 'true'],
            ['key' => 'session_timeout', 'value' => '30'],
            ['key' => 'password_min_length', 'value' => '8'],
            ['key' => 'two_factor_auth', 'value' => 'false'],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
