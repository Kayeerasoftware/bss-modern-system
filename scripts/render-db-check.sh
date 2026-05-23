#!/usr/bin/env bash
set -euo pipefail

render_db_table_exists() {
  local table_name="${1:-}"
  if [[ -z "${table_name}" ]]; then
    return 1
  fi

  php -r '
$table = $argv[1] ?? "";
$host = getenv("DB_HOST") ?: "127.0.0.1";
$port = getenv("DB_PORT") ?: "3306";
$database = getenv("DB_DATABASE") ?: "";
$username = getenv("DB_USERNAME") ?: "";
$password = getenv("DB_PASSWORD") ?: "";
$timeout = getenv("DB_CONNECT_TIMEOUT");

if ($database === "") {
    fwrite(STDERR, "DB_DATABASE is missing\n");
    exit(1);
}

$dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
];

if ($timeout !== false && $timeout !== "") {
    $options[PDO::ATTR_TIMEOUT] = (int) $timeout;
}

$sslCa = getenv("MYSQL_ATTR_SSL_CA");
if ($sslCa !== false && $sslCa !== "") {
    $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
}

$pdo = new PDO($dsn, $username, $password, $options);
$statement = $pdo->prepare("SHOW TABLES LIKE ?");
$statement->execute([$table]);
echo $statement->fetchColumn() ? "1" : "0";
' "${table_name}"
}

render_detect_imported_schema() {
  if [[ "$(render_db_table_exists migrations)" == "1" ]]; then
    return 1
  fi

  local required_tables=(
    users
    roles
    members
    transactions
    savings_accounts
  )

  local table_name
  for table_name in "${required_tables[@]}"; do
    if [[ "$(render_db_table_exists "${table_name}")" != "1" ]]; then
      return 1
    fi
  done

  return 0
}
