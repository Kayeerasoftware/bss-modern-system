<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuditLogsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('audit_logs')->insert([
            ['user' => 'Admin', 'action' => 'Member Added', 'details' => 'Added new member BSS001', 'timestamp' => now()->subHours(2), 'created_at' => now(), 'updated_at' => now()],
            ['user' => 'Admin', 'action' => 'Loan Approved', 'details' => 'Approved loan L001 for UGX 500,000', 'timestamp' => now()->subHours(1), 'created_at' => now(), 'updated_at' => now()],
            ['user' => 'Cashier', 'action' => 'Transaction Processed', 'details' => 'Deposit of UGX 100,000', 'timestamp' => now()->subMinutes(30), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
