@echo off
echo BSS Investment Group System - Health Check
echo ==========================================

echo.
echo 1. Checking PHP installation...
php --version
if %errorlevel% neq 0 (
    echo ERROR: PHP is not installed or not in PATH
    pause
    exit /b 1
)

echo.
echo 2. Checking Composer installation...
composer --version
if %errorlevel% neq 0 (
    echo ERROR: Composer is not installed or not in PATH
    pause
    exit /b 1
)

echo.
echo 3. Checking Laravel installation...
php artisan --version
if %errorlevel% neq 0 (
    echo ERROR: Laravel is not properly installed
    pause
    exit /b 1
)

echo.
echo 4. Checking database connection...
php artisan migrate:status
if %errorlevel% neq 0 (
    echo WARNING: Database connection issues detected
    echo Please check your .env file configuration
)

echo.
echo 5. Checking system routes...
php artisan route:list --compact
if %errorlevel% neq 0 (
    echo ERROR: Route issues detected
)

echo.
echo 6. Testing system health endpoint...
php artisan serve --host=127.0.0.1 --port=8000 &
timeout /t 3 /nobreak > nul
curl -s http://127.0.0.1:8000/api/system/health
if %errorlevel% neq 0 (
    echo WARNING: Health endpoint test failed
    echo This might be normal if curl is not installed
)

echo.
echo ==========================================
echo Health check completed!
echo.
echo If no errors were reported above, your system is ready to use.
echo Start the server with: php artisan serve
echo Access the system at: http://localhost:8000
echo.
pause