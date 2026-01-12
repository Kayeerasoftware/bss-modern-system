@echo off
echo BSS System Complete Fix
echo =======================
echo.

echo Step 1: Clear all caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo.
echo Step 2: Run migrations and seed database...
php artisan migrate:fresh --seed

echo.
echo Step 3: Cache configurations...
php artisan config:cache

echo.
echo Step 4: Start server...
echo Run: php artisan serve
echo Then visit: http://localhost:8000

pause