@echo off
echo Final BSS System seeding attempt...
php artisan migrate:fresh --seed
echo Done!
pause