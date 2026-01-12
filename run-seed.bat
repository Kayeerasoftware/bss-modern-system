@echo off
echo Running corrected BSS System seeding...
echo.

php artisan migrate:fresh --seed

echo.
echo Seeding completed!
pause