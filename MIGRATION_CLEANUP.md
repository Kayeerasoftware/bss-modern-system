# Duplicate Migration Files Removed

The following duplicate migration files have been identified and should be removed:

1. `2024_01_20_000008_create_dividends_table.php` (duplicate of `2024_01_01_000010_create_dividends_table.php`)
2. `2024_12_19_000004_create_shares_table.php` (duplicate of `2024_01_01_000011_create_shares_table.php`)  
3. `2024_12_19_000005_create_savings_history_table.php` (duplicate of `2024_01_01_000007_create_savings_history_table.php`)

These duplicates cause "Table already exists" errors during migration.

Run `fix-migrations.bat` to automatically clean up and migrate.