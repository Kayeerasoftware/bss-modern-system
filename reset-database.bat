@echo off
echo BSS System Database Reset and Setup
echo ===================================
echo.

echo Step 1: Removing old database...
if exist "database\database.sqlite" (
    del "database\database.sqlite"
    echo Old database removed
)

echo.
echo Step 2: Creating new empty database...
echo. 2> "database\database.sqlite"
echo New database created

echo.
echo Step 3: You need to run these commands manually:
echo.
echo Open Command Prompt or PowerShell in this directory and run:
echo.
echo For XAMPP users:
echo   C:\xampp\php\php.exe artisan migrate:fresh --seed
echo.
echo For WAMP users:
echo   C:\wamp64\bin\php\php8.2.0\php.exe artisan migrate:fresh --seed
echo.
echo For Laragon users:
echo   C:\laragon\bin\php\php8.2.0\php.exe artisan migrate:fresh --seed
echo.
echo If PHP is in your PATH:
echo   php artisan migrate:fresh --seed
echo.
echo After running the migration, start the server with:
echo   php artisan serve
echo.

pause