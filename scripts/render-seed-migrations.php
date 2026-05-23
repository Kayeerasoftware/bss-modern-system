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
$tableExists = $pdo->prepare('SHOW TABLES LIKE ?');

foreach ($migrationFiles as $path) {
    $migration = basename($path, '.php');

    if (isset($existingMap[$migration])) {
        continue;
    }

    if (preg_match('/^\d+_create_([a-z0-9_]+)_table$/', $migration, $matches) !== 1) {
        continue;
    }

    $tableName = $matches[1];
    $tableExists->execute([$tableName]);

    if (! $tableExists->fetchColumn()) {
        continue;
    }

    $seeded[] = $migration;
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

fwrite(STDOUT, 'Seeded ' . count($seeded) . " existing create-table migrations.\n");
