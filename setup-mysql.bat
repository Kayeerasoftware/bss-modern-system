@echo off
echo BSS System MySQL Database Setup
echo ================================
echo.

echo Step 1: Create MySQL database
echo Please run these commands in MySQL:
echo.
echo mysql -u root -p
echo CREATE DATABASE IF NOT EXISTS bss_system;
echo USE bss_system;
echo exit;
echo.

echo Step 2: Run Laravel migrations and seeders
echo.
echo For XAMPP:
echo   C:\xampp\php\php.exe artisan migrate:fresh --seed
echo.
echo For WAMP:
echo   C:\wamp64\bin\php\php8.2.0\php.exe artisan migrate:fresh --seed
echo.
echo For Laragon:
echo   C:\laragon\bin\php\php8.2.0\php.exe artisan migrate:fresh --seed
echo.
echo If PHP is in PATH:
echo   php artisan migrate:fresh --seed
echo.

pause