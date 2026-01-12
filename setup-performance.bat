@echo off
echo ========================================
echo BSS System Performance Setup
echo ========================================

echo.
echo Running database migrations...
php artisan migrate --force

echo.
echo Optimizing application...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo Clearing old cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo Setting up performance optimizations...
php artisan optimize

echo.
echo ========================================
echo Performance setup completed!
echo ========================================
echo.
echo Your BSS system now includes:
echo - Enhanced bio data form with auto-save
echo - Optimized database queries with caching
echo - Performance monitoring and rate limiting
echo - Improved error handling and validation
echo - Better user experience with loading states
echo.
echo Access your system at: http://localhost:8000
echo Bio Data Form: http://localhost:8000/bio-data-form
echo Optimized Dashboard: http://localhost:8000/optimized-dashboard
echo.
pause