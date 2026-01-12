@echo off
echo Testing database connection and data...
echo.

REM Try different PHP paths
if exist "C:\xampp\php\php.exe" (
    "C:\xampp\php\php.exe" test-db.php
) else if exist "C:\wamp64\bin\php\php8.2.0\php.exe" (
    "C:\wamp64\bin\php\php8.2.0\php.exe" test-db.php
) else if exist "C:\laragon\bin\php\php8.2.0\php.exe" (
    "C:\laragon\bin\php\php8.2.0\php.exe" test-db.php
) else (
    echo PHP not found in common locations
    echo Please run: php test-db.php manually
)

pause