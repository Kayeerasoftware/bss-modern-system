@echo off
echo FINAL FIX - Display Data in Dashboard
echo ====================================

REM Find PHP
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

echo Clearing caches...
"%PHP_PATH%" artisan view:clear
"%PHP_PATH%" artisan route:clear

echo.
echo ========================================
echo SYSTEM READY!
echo ========================================
echo.
echo Data should now display in dashboard
echo Visit: http://localhost:8000/dashboard
echo.
echo Starting server...
"%PHP_PATH%" artisan serve