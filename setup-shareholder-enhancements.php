<?php

echo "=== BSS Shareholder Dashboard Enhancement Setup ===\n\n";

// Run migrations
echo "Running migrations...\n";
exec('php artisan migrate --path=database/migrations/2024_01_20_000001_create_portfolio_performances_table.php', $output1, $return1);
exec('php artisan migrate --path=database/migrations/2024_01_20_000002_create_investment_opportunities_table.php', $output2, $return2);

if ($return1 === 0 && $return2 === 0) {
    echo "✓ Migrations completed successfully\n\n";
} else {
    echo "✗ Migration failed\n";
    print_r($output1);
    print_r($output2);
    exit(1);
}

// Run seeder
echo "Seeding data...\n";
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$seeder = new Database\Seeders\ShareholderEnhancementsSeeder();
$seeder->run();

echo "✓ Data seeded successfully\n\n";

echo "=== Setup Complete! ===\n";
echo "\nEnhancements Added:\n";
echo "1. Portfolio Performance Tracking\n";
echo "   - Real-time performance metrics\n";
echo "   - Market benchmark comparison\n";
echo "   - Historical performance data\n\n";

echo "2. Dividend Announcements\n";
echo "   - Upcoming dividend notifications\n";
echo "   - Payment schedules\n";
echo "   - Historical dividend records\n\n";

echo "3. Investment Opportunities\n";
echo "   - Active investment projects\n";
echo "   - Expected ROI calculations\n";
echo "   - Risk level assessments\n\n";

echo "API Endpoints Available:\n";
echo "- GET /api/shareholder/performance/{memberId}\n";
echo "- GET /api/shareholder/dividend-announcements\n";
echo "- GET /api/shareholder/investment-opportunities\n";
echo "- GET /api/shareholder/portfolio-analytics/{memberId}\n\n";

echo "Access the enhanced dashboard at: http://localhost:8000/shareholder-dashboard\n";
