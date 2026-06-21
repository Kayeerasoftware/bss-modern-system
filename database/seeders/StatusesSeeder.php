<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusesSeeder extends Seeder
{
    public function run(): void
    {
        // Loan types
        $loanTypes = [
            ['name' => 'Personal Loan',  'description' => 'General personal loan',       'default_interest_rate' => 10.00, 'min_repayment_months' => 3,  'max_repayment_months' => 60],
            ['name' => 'Business Loan',  'description' => 'Business development loan',   'default_interest_rate' => 12.00, 'min_repayment_months' => 6,  'max_repayment_months' => 60],
            ['name' => 'Emergency Loan', 'description' => 'Emergency short-term loan',   'default_interest_rate' => 8.00,  'min_repayment_months' => 1,  'max_repayment_months' => 12],
            ['name' => 'Education Loan', 'description' => 'Education financing loan',    'default_interest_rate' => 7.00,  'min_repayment_months' => 6,  'max_repayment_months' => 48],
        ];
        foreach ($loanTypes as $t) {
            DB::table('loan_types')->updateOrInsert(
                ['name' => $t['name']],
                array_merge($t, ['is_active' => 1])
            );
        }

        // Project statuses
        $projectStatuses = [
            ['name' => 'planning',    'display_name' => 'Planning',     'color' => 'blue'],
            ['name' => 'active',      'display_name' => 'Active',       'color' => 'green'],
            ['name' => 'pending',     'display_name' => 'Pending',      'color' => 'yellow'],
            ['name' => 'completed',   'display_name' => 'Completed',    'color' => 'gray'],
            ['name' => 'on_hold',     'display_name' => 'On Hold',      'color' => 'orange'],
            ['name' => 'cancelled',   'display_name' => 'Cancelled',    'color' => 'red'],
        ];
        foreach ($projectStatuses as $s) {
            DB::table('project_statuses')->updateOrInsert(
                ['name' => $s['name']],
                array_merge($s, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // Loan statuses
        $loanStatuses = [
            ['name' => 'pending',    'display_name' => 'Pending'],
            ['name' => 'approved',   'display_name' => 'Approved'],
            ['name' => 'active',     'display_name' => 'Active'],
            ['name' => 'disbursed',  'display_name' => 'Disbursed'],
            ['name' => 'completed',  'display_name' => 'Completed'],
            ['name' => 'rejected',   'display_name' => 'Rejected'],
            ['name' => 'defaulted',  'display_name' => 'Defaulted'],
        ];
        foreach ($loanStatuses as $s) {
            DB::table('loan_statuses')->updateOrInsert(
                ['name' => $s['name']],
                array_merge($s, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // Fundraising statuses
        $fundraisingStatuses = [
            ['name' => 'draft',     'display_name' => 'Draft'],
            ['name' => 'active',    'display_name' => 'Active'],
            ['name' => 'paused',    'display_name' => 'Paused'],
            ['name' => 'completed', 'display_name' => 'Completed'],
            ['name' => 'cancelled', 'display_name' => 'Cancelled'],
        ];
        foreach ($fundraisingStatuses as $s) {
            DB::table('fundraising_statuses')->updateOrInsert(
                ['name' => $s['name']],
                array_merge($s, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
