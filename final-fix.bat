@echo off
echo BSS System - Final Migration Fix
echo ==================================

echo.
echo Manually delete this file if it exists:
echo database\migrations\2024_01_20_000008_create_dividends_table.php
echo.

echo Then run: php artisan migrate:fresh --seed
echo.

echo Or run this command to delete and migrate:
echo.
echo FOR %%f IN ("database\migrations\2024_01_20_000008_create_dividends_table.php") DO IF EXIST "%%f" DEL "%%f"
echo php artisan migrate:fresh --seed
echo.

pause