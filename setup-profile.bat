@echo off
echo === Profile Picture Setup ===
echo.

echo Creating directories...
if not exist "storage\app\public\profile_pictures" mkdir storage\app\public\profile_pictures
if not exist "public\storage" mkdir public\storage

echo.
echo Running migration...
php artisan migrate --path=database/migrations/2024_01_20_000003_add_profile_picture_to_members.php

echo.
echo Creating storage link...
php artisan storage:link

echo.
echo === Setup Complete ===
echo Profile picture feature is ready!
echo.
pause
