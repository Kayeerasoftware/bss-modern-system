@echo off
echo Testing API Data Retrieval
echo ==========================

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

echo Step 1: Clear routes cache...
"%PHP_PATH%" artisan route:clear

echo.
echo Step 2: Test API endpoint...
"%PHP_PATH%" test-api.php

echo.
echo Step 3: Start server and test manually...
echo Visit: http://localhost:8000/api/dashboard-data
echo.
pause