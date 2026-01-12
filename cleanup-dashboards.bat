@echo off
if exist "resources\views\modern-dashboard.blade.php" del "resources\views\modern-dashboard.blade.php"
if exist "resources\views\bss-dashboard.blade.php" del "resources\views\bss-dashboard.blade.php"
if exist "resources\views\dashboard.blade.php" del "resources\views\dashboard.blade.php"
echo Old dashboard files deleted successfully
pause