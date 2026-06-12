<?php

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('generate_member_account_number')) {
    function generate_member_account_number()
    {
        return \App\Services\System\AccountNumberService::generateMemberAccountNumber();
    }
}

if (!function_exists('generate_savings_account_number')) {
    function generate_savings_account_number()
    {
        return \App\Services\System\AccountNumberService::generateSavingsAccountNumber();
    }
}

if (!function_exists('format_account_number')) {
    function format_account_number($type, $number = null)
    {
        if ($type === 'member') {
            return $number ? $number : generate_member_account_number();
        } elseif ($type === 'savings') {
            return $number ? $number : generate_savings_account_number();
        }
        return $number;
    }
}

if (!function_exists('table_sum_or_zero')) {
    function table_sum_or_zero(string $table, string $column, float|int $default = 0): float|int
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable($table)) {
                return $default;
            }

            return (float) \Illuminate\Support\Facades\DB::table($table)->sum($column);
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (!function_exists('table_average_or_zero')) {
    function table_average_or_zero(string $table, string $column, float|int $default = 0): float|int
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable($table)) {
                return $default;
            }

            $value = \Illuminate\Support\Facades\DB::table($table)->avg($column);

            return $value === null ? $default : (float) $value;
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (!function_exists('resolve_database_connection')) {
    function resolve_database_connection(): string
    {
        $connection = strtolower((string) env('DB_CONNECTION', 'mysql'));
        $dbUrl = strtolower((string) env('DB_URL', ''));
        $dbPort = (string) env('DB_PORT', '');
        $mysqlSslCa = trim((string) env('MYSQL_ATTR_SSL_CA', ''));
        $aivenCaPem = trim((string) env('AIVEN_CA_PEM', ''));

        if ($dbUrl !== '' && (str_starts_with($dbUrl, 'mysql:') || str_starts_with($dbUrl, 'mariadb:'))) {
            return 'mysql';
        }

        if ($dbUrl !== '' && (str_starts_with($dbUrl, 'pgsql:') || str_starts_with($dbUrl, 'postgres:'))) {
            return 'pgsql';
        }

        if ($connection === 'pgsql') {
            $mysqlHints = [
                $dbPort === '3306',
                $mysqlSslCa !== '',
                $aivenCaPem !== '',
            ];

            foreach ($mysqlHints as $hint) {
                if ($hint) {
                    return 'mysql';
                }
            }

            // This application ships MySQL-only migrations and schema DDL.
            // Prefer MySQL unless a PostgreSQL URL is explicitly configured.
            return 'mysql';
        }

        return match ($connection) {
            'mysql', 'mariadb', 'pgsql', 'sqlite', 'sqlsrv' => $connection,
            default => 'mysql',
        };
    }
}
