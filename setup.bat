@echo off
echo Setting up BSS-2025 Investment Group System...

echo Running migrations...
php artisan migrate:fresh --seed

echo Starting development server...
php artisan serve

pause