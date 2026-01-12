@echo off
echo Running BSS System Migration...
echo.

echo Clearing previous migration state...
php artisan migrate:reset

echo.
echo Running fresh migrations with seeding...
php artisan migrate:fresh --seed

echo.
echo Migration completed!
pause