<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = [
            [
                'table' => 'transactions',
                'name' => 'idx_transactions_member_date',
                'sql' => 'CREATE INDEX idx_transactions_member_date ON transactions(member_id, transaction_date)',
            ],
            [
                'table' => 'transactions',
                'name' => 'idx_transactions_status_date',
                'sql' => 'CREATE INDEX idx_transactions_status_date ON transactions(status_id, transaction_date)',
            ],
            [
                'table' => 'loans',
                'name' => 'idx_loans_member_status',
                'sql' => 'CREATE INDEX idx_loans_member_status ON loans(member_id, status_id)',
            ],
            [
                'table' => 'loans',
                'name' => 'idx_loans_dates',
                'sql' => 'CREATE INDEX idx_loans_dates ON loans(application_date, approval_date, disbursement_date)',
            ],
            [
                'table' => 'shares',
                'name' => 'idx_shares_member_status',
                'sql' => 'CREATE INDEX idx_shares_member_status ON shares(member_id, status_id)',
            ],
            [
                'table' => 'member_dividends',
                'name' => 'idx_dividends_member_status',
                'sql' => 'CREATE INDEX idx_dividends_member_status ON member_dividends(member_id, status)',
            ],
            [
                'table' => 'projects',
                'name' => 'idx_projects_status_date',
                'sql' => 'CREATE INDEX idx_projects_status_date ON projects(status_id, start_date)',
            ],
            [
                'table' => 'meetings',
                'name' => 'idx_meetings_status_date',
                'sql' => 'CREATE INDEX idx_meetings_status_date ON meetings(status_id, scheduled_at)',
            ],
            [
                'table' => 'documents',
                'name' => 'idx_documents_member_expiry',
                'sql' => 'CREATE INDEX idx_documents_member_expiry ON documents(member_id, expiry_date)',
            ],
            [
                'table' => 'notification_receipts',
                'name' => 'idx_notifications_member_read',
                'sql' => 'CREATE INDEX idx_notifications_member_read ON notification_receipts(member_id, is_read)',
            ],
            [
                'table' => 'audit_logs',
                'name' => 'idx_audit_logs_entity',
                'sql' => 'CREATE INDEX idx_audit_logs_entity ON audit_logs(entity_type_id, entity_id, created_at)',
            ],
            [
                'table' => 'audit_logs',
                'name' => 'idx_audit_logs_user_date',
                'sql' => 'CREATE INDEX idx_audit_logs_user_date ON audit_logs(user_id, created_at)',
            ],
        ];

        $database = DB::getDatabaseName();

        foreach ($indexes as $index) {
            $exists = DB::selectOne(
                'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
                [$database, $index['table'], $index['name']]
            );

            if (!$exists) {
                DB::unprepared($index['sql']);
            }
        }
    }

    public function down(): void
    {
        $indexes = [
            ['table' => 'transactions', 'name' => 'idx_transactions_member_date'],
            ['table' => 'transactions', 'name' => 'idx_transactions_status_date'],
            ['table' => 'loans', 'name' => 'idx_loans_member_status'],
            ['table' => 'loans', 'name' => 'idx_loans_dates'],
            ['table' => 'shares', 'name' => 'idx_shares_member_status'],
            ['table' => 'member_dividends', 'name' => 'idx_dividends_member_status'],
            ['table' => 'projects', 'name' => 'idx_projects_status_date'],
            ['table' => 'meetings', 'name' => 'idx_meetings_status_date'],
            ['table' => 'documents', 'name' => 'idx_documents_member_expiry'],
            ['table' => 'notification_receipts', 'name' => 'idx_notifications_member_read'],
            ['table' => 'audit_logs', 'name' => 'idx_audit_logs_entity'],
            ['table' => 'audit_logs', 'name' => 'idx_audit_logs_user_date'],
        ];

        $database = DB::getDatabaseName();

        foreach ($indexes as $index) {
            $exists = DB::selectOne(
                'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
                [$database, $index['table'], $index['name']]
            );

            if ($exists) {
                DB::statement(sprintf(
                    'DROP INDEX `%s` ON `%s`',
                    $index['name'],
                    $index['table']
                ));
            }
        }
    }
};
