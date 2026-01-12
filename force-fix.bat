@echo off
echo FORCE FIX - BSS System Data Issue
echo ==================================

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

echo Step 1: Clear caches...
"%PHP_PATH%" artisan config:clear
"%PHP_PATH%" artisan cache:clear

echo.
echo Step 2: Force insert data directly...
"%PHP_PATH%" force-insert-data.php

echo.
echo Step 3: Clear cache again...
"%PHP_PATH%" artisan cache:clear

echo.
echo ========================================
echo DATA FORCE INSERTED!
echo ========================================
echo.
echo Start server: %PHP_PATH% artisan serve
echo Visit: http://localhost:8000
echo Login: admin@bss.com / admin123
echo.
pause