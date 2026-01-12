@echo off
echo ========================================
echo BSS Investment Group - Comprehensive Setup
echo ========================================

echo.
echo [1/6] Installing Composer Dependencies...
call composer install --no-dev --optimize-autoloader

echo.
echo [2/6] Generating Application Key...
call php artisan key:generate

echo.
echo [3/6] Running Database Migrations...
call php artisan migrate:fresh

echo.
echo [4/6] Seeding Database with Sample Data...
call php artisan db:seed --class=ComprehensiveSeeder

echo.
echo [5/6] Clearing Application Cache...
call php artisan cache:clear
call php artisan config:clear
call php artisan view:clear

echo.
echo [6/6] Installing NPM Dependencies...
call npm install
call npm run build

echo.
echo ========================================
echo ✅ COMPREHENSIVE SETUP COMPLETE!
echo ========================================
echo.
echo 🚀 Your BSS Investment Group system is ready!
echo.
echo 📊 Features Available:
echo   • Member Management System
echo   • Loan Processing & Tracking
echo   • Savings Account Management
echo   • Transaction Processing
echo   • Project Management
echo   • Share & Dividend Management
echo   • Meeting Scheduling
echo   • Document Management
echo   • Comprehensive Analytics
echo   • Notification System
echo   • System Settings
echo.
echo 🌐 Access your system at: http://localhost:8000
echo 👤 Sample Login Credentials:
echo   Email: admin@bss.com
echo   Password: password123
echo.
echo 📚 Documentation: COMPREHENSIVE_FEATURES.md
echo.
echo To start the server, run: php artisan serve
echo ========================================

pause