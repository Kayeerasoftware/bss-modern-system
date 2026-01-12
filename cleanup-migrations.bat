@echo off
echo Removing duplicate migration files...
del /F "database\migrations\2024_01_20_000008_create_dividends_table.php" 2>nul
del /F "database\migrations\2024_12_19_000004_create_shares_table.php" 2>nul
echo Done! Run: php artisan migrate:fresh --seed
