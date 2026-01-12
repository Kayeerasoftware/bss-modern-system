@echo off
echo Seeding BSS System with real data...
echo.

php artisan db:seed --class=BSSSeeder

echo.
echo Database seeded successfully!
echo All charts now use real database data.
pause
