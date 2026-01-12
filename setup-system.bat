@echo off
echo Setting up BSS Investment Group System...

echo.
echo 1. Installing PHP dependencies...
call composer install --no-dev --optimize-autoloader

echo.
echo 2. Setting up environment...
if not exist .env (
    copy .env.example .env
    echo Environment file created from example
)

echo.
echo 3. Generating application key...
call php artisan key:generate

echo.
echo 4. Running database migrations...
call php artisan migrate:fresh --seed

echo.
echo 5. Clearing caches...
call php artisan config:clear
call php artisan cache:clear
call php artisan view:clear

echo.
echo 6. Optimizing application...
call php artisan config:cache
call php artisan route:cache

echo.
echo BSS Investment Group System setup completed!
echo.
echo You can now start the development server with:
echo php artisan serve
echo.
echo Default login credentials:
echo Admin: admin@bss.com / admin123
echo Manager: manager@bss.com / manager123
echo Member: member@bss.com / member123
echo.
echo Access the system at: http://localhost:8000
echo.
pause