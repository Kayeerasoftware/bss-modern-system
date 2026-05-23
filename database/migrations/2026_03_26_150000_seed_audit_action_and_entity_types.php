<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('audit_action_types')) {
            $actions = [
                ['name' => 'view', 'display_name' => 'View', 'description' => 'View or read records', 'severity' => 'info'],
                ['name' => 'check', 'display_name' => 'Check', 'description' => 'Query or filter records', 'severity' => 'info'],
                ['name' => 'create', 'display_name' => 'Create', 'description' => 'Create new records', 'severity' => 'info'],
                ['name' => 'update', 'display_name' => 'Update', 'description' => 'Update existing records', 'severity' => 'warning'],
                ['name' => 'delete', 'display_name' => 'Delete', 'description' => 'Delete records', 'severity' => 'critical'],
                ['name' => 'download', 'display_name' => 'Download', 'description' => 'Download or export data', 'severity' => 'info'],
                ['name' => 'role_switch', 'display_name' => 'Role Switch', 'description' => 'Switch user role/session', 'severity' => 'warning'],
            ];

            foreach ($actions as $action) {
                DB::table('audit_action_types')->updateOrInsert(
                    ['name' => $action['name']],
                    [
                        'display_name' => $action['display_name'],
                        'description' => $action['description'],
                        'severity' => $action['severity'],
                    ]
                );
            }
        }

        if (Schema::hasTable('entity_types')) {
            $entities = [
                ['name' => 'user', 'display_name' => 'User', 'table_name' => 'users'],
                ['name' => 'member', 'display_name' => 'Member', 'table_name' => 'members'],
                ['name' => 'loan', 'display_name' => 'Loan', 'table_name' => 'loans'],
                ['name' => 'loan_application', 'display_name' => 'Loan Application', 'table_name' => 'loan_applications'],
                ['name' => 'transaction', 'display_name' => 'Transaction', 'table_name' => 'transactions'],
                ['name' => 'savings', 'display_name' => 'Savings', 'table_name' => 'savings'],
            ];

            foreach ($entities as $entity) {
                DB::table('entity_types')->updateOrInsert(
                    ['name' => $entity['name']],
                    [
                        'display_name' => $entity['display_name'],
                        'table_name' => $entity['table_name'],
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('audit_action_types')) {
            DB::table('audit_action_types')->whereIn('name', [
                'view',
                'check',
                'create',
                'update',
                'delete',
                'download',
                'role_switch',
            ])->delete();
        }

        if (Schema::hasTable('entity_types')) {
            DB::table('entity_types')->whereIn('name', [
                'user',
                'member',
                'loan',
                'loan_application',
                'transaction',
                'savings',
            ])->delete();
        }
    }
};
