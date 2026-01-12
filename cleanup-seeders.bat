@echo off
echo Cleaning up unnecessary seeder files...

if exist "database\seeders\BSSSeeder.php" (
    del "database\seeders\BSSSeeder.php"
    echo Deleted BSSSeeder.php
)

if exist "database\seeders\ComprehensiveSeeder.php" (
    del "database\seeders\ComprehensiveSeeder.php"
    echo Deleted ComprehensiveSeeder.php
)

echo Seeder cleanup completed!
pause