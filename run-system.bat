@echo off
echo ========================================
echo BSS System - Quick Start
echo ========================================

echo [1/3] Running Migrations...
php artisan migrate:fresh --seed

echo [2/3] Clearing Cache...
php artisan cache:clear

echo [3/3] Starting Server...
echo.
echo ✅ System Ready!
echo 🌐 Main Dashboard: http://localhost:8000
echo 👤 Admin Panel: http://localhost:8000/admin
echo.
php artisan serve