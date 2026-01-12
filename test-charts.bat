@echo off
echo Testing BSS Charts Dashboard...
echo.

echo Starting Laravel server...
start /B php artisan serve

echo Waiting for server to start...
timeout /t 3 /nobreak > nul

echo Testing charts dashboard...
curl -s http://localhost:8000/charts > nul
if %errorlevel% == 0 (
    echo ✓ Charts dashboard is accessible
) else (
    echo ✗ Charts dashboard failed to load
)

echo Testing analytics API...
curl -s http://localhost:8000/api/analytics/dashboard > nul
if %errorlevel% == 0 (
    echo ✓ Analytics API is working
) else (
    echo ✗ Analytics API failed
)

echo.
echo Charts dashboard test completed!
echo Visit: http://localhost:8000/charts
echo.
pause