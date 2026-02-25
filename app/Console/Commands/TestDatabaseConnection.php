<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDatabaseConnection extends Command
{
    protected $signature = 'db:test';
    protected $description = 'Test database connection';

    public function handle()
    {
        try {
            DB::connection()->getPdo();
            $this->info('✓ Database connection successful!');
            $this->info('Database: ' . DB::connection()->getDatabaseName());
            return 0;
        } catch (\Exception $e) {
            $this->error('✗ Database connection failed!');
            $this->error($e->getMessage());
            return 1;
        }
    }
}
