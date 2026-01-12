@echo off
echo Clearing Laravel caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo Done!
pause