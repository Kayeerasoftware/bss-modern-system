@echo off
echo ========================================
echo BSS Admin Panel Setup
echo ========================================

echo [1/4] Running Database Migrations...
call php artisan migrate:fresh

echo [2/4] Seeding Database...
call php artisan db:seed --class=MemberSeeder

echo [3/4] Clearing Cache...
call php artisan cache:clear
call php artisan config:clear

echo [4/4] Starting Server...
echo.
echo ✅ Admin Panel Ready!
echo 🌐 Access: http://localhost:8000/admin
echo 📊 Dashboard: http://localhost:8000
echo.
call php artisan serve