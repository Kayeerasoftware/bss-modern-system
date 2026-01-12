@echo off
echo Manual Database Setup for BSS System
echo =====================================
echo.

echo Step 1: Backing up existing database...
if exist "database\database.sqlite" (
    copy "database\database.sqlite" "database\database.sqlite.backup"
    echo Database backed up successfully
) else (
    echo No existing database found
)

echo.
echo Step 2: Creating fresh database...
del "database\database.sqlite" 2>nul
echo. > "database\database.sqlite"

echo.
echo Step 3: Running migrations and seeders...
echo Please run the following commands manually:
echo.
echo 1. Open command prompt in this directory
echo 2. Run: php artisan migrate:fresh --seed
echo.
echo Alternative: If PHP is not in PATH, try:
echo - C:\xampp\php\php.exe artisan migrate:fresh --seed
echo - C:\wamp64\bin\php\php8.2.0\php.exe artisan migrate:fresh --seed  
echo - C:\laragon\bin\php\php8.2.0\php.exe artisan migrate:fresh --seed
echo.

pause