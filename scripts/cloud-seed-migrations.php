<?php

declare(strict_types=1);

use App\Services\Deployment\MigrationHistorySeeder;
use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$seeded = $app->make(MigrationHistorySeeder::class)->seed();

if ($seeded === []) {
    fwrite(STDOUT, "No baseline migrations needed seeding.\n");
    exit(0);
}

fwrite(STDOUT, 'Seeded ' . count($seeded) . " existing migration entries.\n");
