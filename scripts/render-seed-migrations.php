<?php

declare(strict_types=1);

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$database = getenv('DB_DATABASE') ?: '';
$username = getenv('DB_USERNAME') ?: '';
$password = getenv('DB_PASSWORD') ?: '';
$timeout = getenv('DB_CONNECT_TIMEOUT');
$sslCa = getenv('MYSQL_ATTR_SSL_CA');

if ($database === '') {
    fwrite(STDERR, "DB_DATABASE is missing\n");
    exit(1);
}

$dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
];

if ($timeout !== false && $timeout !== '') {
    $options[PDO::ATTR_TIMEOUT] = (int) $timeout;
}

if ($sslCa !== false && $sslCa !== '') {
    $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
}

$pdo = new PDO($dsn, $username, $password, $options);

$pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `migration` VARCHAR(255) NOT NULL,
    `batch` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);

$existing = $pdo->query('SELECT `migration` FROM `migrations`')->fetchAll(PDO::FETCH_COLUMN) ?: [];
$existingMap = array_fill_keys($existing, true);

$migrationsPath = __DIR__ . '/../database/migrations';
$migrationFiles = glob($migrationsPath . '/*.php') ?: [];
sort($migrationFiles, SORT_STRING);

$seeded = [];
$databaseName = $pdo->query('SELECT DATABASE()')->fetchColumn();
if (! is_string($databaseName) || $databaseName === '') {
    fwrite(STDERR, "Unable to resolve active database name\n");
    exit(1);
}

$exists = static function (string $sql, array $params) use ($pdo): bool {
    $statement = $pdo->prepare($sql);
    $statement->execute($params);

    return $statement->fetchColumn() !== false;
};

$tableExists = static fn (string $table): bool => $exists(
    'SELECT 1 FROM information_schema.tables WHERE table_schema = ? AND table_name = ? LIMIT 1',
    [$databaseName, $table]
);
$viewExists = static fn (string $view): bool => $exists(
    'SELECT 1 FROM information_schema.views WHERE table_schema = ? AND table_name = ? LIMIT 1',
    [$databaseName, $view]
);
$triggerExists = static fn (string $trigger): bool => $exists(
    'SELECT 1 FROM information_schema.triggers WHERE trigger_schema = ? AND trigger_name = ? LIMIT 1',
    [$databaseName, $trigger]
);
$indexExists = static fn (string $table, string $index): bool => $exists(
    'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
    [$databaseName, $table, $index]
);

foreach ($migrationFiles as $path) {
    $migration = basename($path, '.php');

    if (isset($existingMap[$migration])) {
        continue;
    }

    if (preg_match('/^\d+_create_([a-z0-9_]+)_table$/', $migration, $matches) === 1) {
        $tableName = $matches[1];

        if ($tableExists($tableName)) {
            $seeded[] = $migration;
        }

        continue;
    }

    if ($migration === '2026_03_15_000001_create_member_category_balances_table' && $tableExists('member_category_balances')) {
        $seeded[] = $migration;
        continue;
    }

    if ($migration === '2026_03_13_010000_create_bss_views') {
        $views = [
            'v_member_summary',
            'v_loan_details',
            'v_transaction_summary',
            'v_dashboard_stats',
            'v_member_financial_report',
            'v_loan_performance',
            'v_transaction_volume',
        ];

        $allViewsExist = true;
        foreach ($views as $view) {
            if (! $viewExists($view)) {
                $allViewsExist = false;
                break;
            }
        }

        if ($allViewsExist) {
            $seeded[] = $migration;
        }

        continue;
    }

    if ($migration === '2026_03_13_010100_create_bss_triggers') {
        $triggers = [
            'after_user_insert',
            'before_member_delete',
            'after_transaction_complete',
            'before_member_address_insert',
            'before_member_address_update',
            'after_loan_repayment',
            'before_user_delete',
            'after_transaction_insert',
        ];

        $allTriggersExist = true;
        foreach ($triggers as $trigger) {
            if (! $triggerExists($trigger)) {
                $allTriggersExist = false;
                break;
            }
        }

        if ($allTriggersExist) {
            $seeded[] = $migration;
        }

        continue;
    }

    if ($migration === '2026_03_13_010200_create_bss_indexes') {
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

        $allIndexesExist = true;
        foreach ($indexes as $index) {
            if (! $indexExists($index['table'], $index['name'])) {
                $allIndexesExist = false;
                break;
            }
        }

        if ($allIndexesExist) {
            $seeded[] = $migration;
        }

        continue;
    }

    if ($migration === '2026_03_14_044717_add_performance_indexes') {
        $indexes = [
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
        ];

        $allIndexesExist = true;
        foreach ($indexes as $index) {
            if (! $indexExists($index['table'], $index['name'])) {
                $allIndexesExist = false;
                break;
            }
        }

        if ($allIndexesExist) {
            $seeded[] = $migration;
        }

        continue;
    }

    if ($migration === '2026_04_01_000001_add_performance_indexes_v2') {
        $indexes = [
            ['table' => 'savings_accounts', 'name' => 'savings_accounts_member_id_index'],
            ['table' => 'savings_accounts', 'name' => 'savings_accounts_status_index'],
            ['table' => 'savings_accounts', 'name' => 'savings_accounts_is_joint_index'],
            ['table' => 'savings_accounts', 'name' => 'savings_accounts_maturity_date_index'],
            ['table' => 'savings_accounts', 'name' => 'savings_accounts_current_balance_index'],
            ['table' => 'savings_accounts', 'name' => 'savings_accounts_opening_date_index'],
        ];

        $allIndexesExist = true;
        foreach ($indexes as $index) {
            if (! $indexExists($index['table'], $index['name'])) {
                $allIndexesExist = false;
                break;
            }
        }

        if ($allIndexesExist) {
            $seeded[] = $migration;
        }

        continue;
    }
}

if ($seeded === []) {
    fwrite(STDOUT, "No baseline migrations needed seeding.\n");
    exit(0);
}

$nextBatch = (int) $pdo->query('SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`')->fetchColumn();
$insert = $pdo->prepare('INSERT INTO `migrations` (`migration`, `batch`) VALUES (?, ?)');

foreach ($seeded as $migration) {
    $insert->execute([$migration, $nextBatch]);
}

fwrite(STDOUT, 'Seeded ' . count($seeded) . " existing migration entries.\n");
