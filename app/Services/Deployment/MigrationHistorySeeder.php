<?php

namespace App\Services\Deployment;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrationHistorySeeder
{
    /**
     * Seed the migrations table for schema objects that already exist.
     *
     * This keeps Laravel from replaying the full MySQL-oriented migration set
     * on environments where the schema has already been provisioned/imported.
     *
     * @return array<int, string>
     */
    public function seed(): array
    {
        if (!Schema::hasTable('migrations')) {
            Schema::create('migrations', function (Blueprint $table): void {
                $table->id();
                $table->string('migration');
                $table->integer('batch');
            });
        }

        $existing = DB::table('migrations')->pluck('migration')->all();
        $existingMap = array_fill_keys($existing, true);
        $seeded = [];

        $mark = static function (string $migration) use (&$seeded, $existingMap): void {
            if (!isset($existingMap[$migration])) {
                $seeded[] = $migration;
            }
        };

        $migrationFiles = glob(database_path('migrations/*.php')) ?: [];
        sort($migrationFiles, SORT_STRING);

        foreach ($migrationFiles as $path) {
            $migration = basename($path, '.php');

            if (isset($existingMap[$migration])) {
                continue;
            }

            if (preg_match('/^\d+_create_([a-z0-9_]+)_table$/', $migration, $matches) === 1) {
                if (Schema::hasTable($matches[1])) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_03_13_010000_create_bss_views') {
                if ($this->allViewsExist([
                    'v_member_summary',
                    'v_loan_details',
                    'v_transaction_summary',
                    'v_dashboard_stats',
                    'v_member_financial_report',
                    'v_loan_performance',
                    'v_transaction_volume',
                ])) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_03_13_010100_create_bss_triggers') {
                if ($this->allTriggersExist([
                    'after_user_insert',
                    'before_member_delete',
                    'after_transaction_complete',
                    'before_member_address_insert',
                    'before_member_address_update',
                    'after_loan_repayment',
                    'before_user_delete',
                    'after_transaction_insert',
                ])) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_03_13_010200_create_bss_indexes') {
                if ($this->allIndexesExist([
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
                ])) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_03_13_000142_add_profile_picture_to_users_table') {
                if (Schema::hasColumn('users', 'profile_picture')) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_03_15_000002_add_fundraising_transaction_categories') {
                if ($this->tableHasRow('transaction_categories', ['name' => 'fundraising_deposit'])
                    && $this->tableHasRow('transaction_categories', ['name' => 'fundraising_transfer'])) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_03_14_044717_add_performance_indexes') {
                if ($this->allIndexesExist([
                    ['table' => 'audit_logs', 'name' => 'audit_logs_user_id_index'],
                    ['table' => 'audit_logs', 'name' => 'audit_logs_created_at_index'],
                    ['table' => 'audit_logs', 'name' => 'audit_logs_user_id_created_at_index'],
                    ['table' => 'members', 'name' => 'members_user_id_index'],
                    ['table' => 'members', 'name' => 'members_membership_status_index'],
                    ['table' => 'transactions', 'name' => 'transactions_member_id_index'],
                    ['table' => 'transactions', 'name' => 'transactions_status_id_index'],
                    ['table' => 'transactions', 'name' => 'transactions_transaction_type_id_index'],
                    ['table' => 'loans', 'name' => 'loans_member_id_index'],
                    ['table' => 'loans', 'name' => 'loans_status_id_index'],
                    ['table' => 'users', 'name' => 'users_role_id_index'],
                    ['table' => 'users', 'name' => 'users_status_index'],
                ])) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_03_16_000001_add_account_numbers_to_system') {
                if (Schema::hasColumn('members', 'member_account_number')
                    && $this->indexExists('members', 'idx_members_account_number')) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_03_24_000001_add_loan_disbursement_type_and_status') {
                if ($this->tableHasRow('loan_statuses', ['name' => 'disbursed'])
                    && $this->tableHasRow('transaction_types', ['name' => 'loan_disbursement'])
                    && $this->tableHasRow('transaction_categories', ['name' => 'loan_disbursement'])) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_03_24_000001_update_user_insert_trigger_format') {
                if ($this->triggerExists('after_user_insert')) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_03_25_000001_reclassify_fundraising_deposits') {
                if ($this->tableHasRow('transaction_categories', ['name' => 'fundraising_deposit'])
                    && ! $this->tableHasRow('transaction_categories', ['name' => 'savings_deposit'])) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_03_25_000002_drop_legacy_member_balance_columns') {
                if (!Schema::hasColumn('members', 'savings')
                    && !Schema::hasColumn('members', 'savings_balance')
                    && !Schema::hasColumn('members', 'balance')) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_03_26_000001_create_loan_settings_table') {
                if (Schema::hasTable('loan_settings')) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_03_26_150000_seed_audit_action_and_entity_types') {
                if ($this->allNamedRowsExist('audit_action_types', [
                        'view',
                        'check',
                        'create',
                        'update',
                        'delete',
                        'download',
                        'role_switch',
                    ])
                    && $this->allNamedRowsExist('entity_types', [
                        'user',
                        'member',
                        'loan',
                        'loan_application',
                        'transaction',
                        'savings',
                    ])) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_04_01_000001_add_performance_indexes_v2') {
                if ($this->allIndexesExist([
                    ['table' => 'savings_accounts', 'name' => 'savings_accounts_member_id_index'],
                    ['table' => 'savings_accounts', 'name' => 'savings_accounts_status_index'],
                    ['table' => 'savings_accounts', 'name' => 'savings_accounts_is_joint_index'],
                    ['table' => 'savings_accounts', 'name' => 'savings_accounts_maturity_date_index'],
                    ['table' => 'savings_accounts', 'name' => 'savings_accounts_current_balance_index'],
                    ['table' => 'savings_accounts', 'name' => 'savings_accounts_opening_date_index'],
                ])) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_05_23_012216_add_savings_column_to_members_table') {
                if (Schema::hasColumn('members', 'savings_transaction_id')) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_05_23_020000_replace_members_savings_with_savings_transaction_id') {
                if (Schema::hasColumn('members', 'savings_transaction_id') && !Schema::hasColumn('members', 'savings')) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_05_23_030000_replace_member_category_balances_with_derived_view') {
                if ($this->viewExists('member_category_balances')) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_05_23_040000_migrate_members_savings_to_savings_accounts_and_drop_column') {
                if (!Schema::hasColumn('members', 'savings')
                    && Schema::hasTable('savings_accounts')
                    && Schema::hasTable('savings_transactions')) {
                    $mark($migration);
                }

                continue;
            }

            if ($migration === '2026_05_23_050000_allow_force_delete_of_trashed_members') {
                if ($this->triggerExists('before_member_delete')) {
                    $mark($migration);
                }
            }
        }

        if ($seeded === []) {
            return [];
        }

        $nextBatch = (int) DB::table('migrations')->max('batch') + 1;
        if ($nextBatch < 1) {
            $nextBatch = 1;
        }

        foreach ($seeded as $migration) {
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $nextBatch,
            ]);
        }

        return $seeded;
    }

    private function tableHasRow(string $table, array $conditions): bool
    {
        if (!Schema::hasTable($table)) {
            return false;
        }

        return DB::table($table)->where($conditions)->exists();
    }

    private function allNamedRowsExist(string $table, array $names): bool
    {
        if (!Schema::hasTable($table)) {
            return false;
        }

        foreach ($names as $name) {
            if (!DB::table($table)->where('name', $name)->exists()) {
                return false;
            }
        }

        return true;
    }

    private function viewExists(string $view): bool
    {
        $schema = $this->currentSchema();

        return DB::selectOne(
            'SELECT 1 FROM information_schema.views WHERE table_schema = ? AND table_name = ? LIMIT 1',
            [$schema, $view]
        ) !== null;
    }

    private function triggerExists(string $trigger): bool
    {
        $schema = $this->currentSchema();

        return DB::selectOne(
            'SELECT 1 FROM information_schema.triggers WHERE trigger_schema = ? AND trigger_name = ? LIMIT 1',
            [$schema, $trigger]
        ) !== null;
    }

    private function allViewsExist(array $views): bool
    {
        foreach ($views as $view) {
            if (! $this->viewExists($view)) {
                return false;
            }
        }

        return true;
    }

    private function allTriggersExist(array $triggers): bool
    {
        foreach ($triggers as $trigger) {
            if (! $this->triggerExists($trigger)) {
                return false;
            }
        }

        return true;
    }

    private function allIndexesExist(array $indexes): bool
    {
        foreach ($indexes as $index) {
            if (!isset($index['table'], $index['name'])) {
                return false;
            }

            if (! $this->indexExists($index['table'], $index['name'])) {
                return false;
            }
        }

        return true;
    }

    private function indexExists(string $table, string $index): bool
    {
        $schema = $this->currentSchema();
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            return DB::selectOne(
                'SELECT 1 FROM pg_indexes WHERE schemaname = ? AND tablename = ? AND indexname = ? LIMIT 1',
                [$schema, $table, $index]
            ) !== null;
        }

        return DB::selectOne(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$schema, $table, $index]
        ) !== null;
    }

    private function currentSchema(): string
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            $row = DB::selectOne('SELECT COALESCE(current_schema(), \'public\') AS schema_name');

            return is_object($row) && isset($row->schema_name) && is_string($row->schema_name) && $row->schema_name !== ''
                ? $row->schema_name
                : 'public';
        }

        $row = DB::selectOne('SELECT DATABASE() AS schema_name');

        return is_object($row) && isset($row->schema_name) && is_string($row->schema_name) && $row->schema_name !== ''
            ? $row->schema_name
            : 'default';
    }
}
