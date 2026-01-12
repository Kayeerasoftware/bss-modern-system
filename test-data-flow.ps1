# Test API Data Retrieval
Write-Host "Testing API Data Retrieval" -ForegroundColor Green
Write-Host "=========================="

# Find PHP executable
$phpPath = $null
$possiblePaths = @(
    "C:\xampp\php\php.exe",
    "C:\wamp64\bin\php\php8.2.0\php.exe", 
    "C:\laragon\bin\php\php8.2.0\php.exe"
)

foreach ($path in $possiblePaths) {
    if (Test-Path $path) {
        $phpPath = $path
        break
    }
}

if (-not $phpPath) {
    try {
        php --version | Out-Null
        $phpPath = "php"
    } catch {
        Write-Host "ERROR: PHP not found" -ForegroundColor Red
        Read-Host "Press Enter to exit"
        exit 1
    }
}

Write-Host "Using PHP: $phpPath"

Write-Host "`nStep 1: Clear routes cache..."
& $phpPath artisan route:clear

Write-Host "`nStep 2: Test API endpoint..."
& $phpPath test-api.php

Write-Host "`nStep 3: Manual test URL:"
Write-Host "http://localhost:8000/api/dashboard-data" -ForegroundColor Yellow

Read-Host "`nPress Enter to continue"