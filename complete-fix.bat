@echo off
echo BSS System - Complete Fix
echo ==========================

echo.
echo Step 1: Removing duplicate migration files...
if exist "database\migrations\2024_01_20_000008_create_dividends_table.php" (
    del "database\migrations\2024_01_20_000008_create_dividends_table.php"
    echo Removed duplicate dividends migration
)

if exist "database\migrations\2024_12_19_000004_create_shares_table.php" (
    del "database\migrations\2024_12_19_000004_create_shares_table.php"
    echo Removed duplicate shares migration
)

if exist "database\migrations\2024_12_19_000005_create_savings_history_table.php" (
    del "database\migrations\2024_12_19_000005_create_savings_history_table.php"
    echo Removed duplicate savings history migration
)

echo.
echo Step 2: Clearing caches...
php artisan config:clear
php artisan cache:clear

echo.
echo Step 3: Running migration...
php artisan migrate:fresh --seed

if %errorlevel% equ 0 (
    echo.
    echo ✅ SUCCESS! BSS System is ready!
    echo.
    echo Start server: php artisan serve
    echo Access: http://localhost:8000
    echo Login: admin@bss.com / admin123
) else (
    echo.
    echo ❌ Migration failed
)

pause