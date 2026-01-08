@echo off
echo Starting BSS System Server...
echo =============================

REM Find PHP executable
set PHP_PATH=
if exist "C:\xampp\php\php.exe" set PHP_PATH=C:\xampp\php\php.exe
if exist "C:\wamp64\bin\php\php8.2.0\php.exe" set PHP_PATH=C:\wamp64\bin\php\php8.2.0\php.exe
if exist "C:\laragon\bin\php\php8.2.0\php.exe" set PHP_PATH=C:\laragon\bin\php\php8.2.0\php.exe

if "%PHP_PATH%"=="" (
    php --version >nul 2>&1
    if %errorlevel%==0 (
        set PHP_PATH=php
    ) else (
        echo ERROR: PHP not found
        pause
        exit /b 1
    )
)

echo Using PHP: %PHP_PATH%
echo.
echo Server starting at: http://localhost:8000
echo.
echo Login credentials:
echo Email: admin@bss.com
echo Password: admin123
echo.
echo Press Ctrl+C to stop the server
echo.

"%PHP_PATH%" artisan serve