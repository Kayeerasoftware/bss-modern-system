<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusesSeeder extends Seeder
{
    public function run(): void
    {
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
