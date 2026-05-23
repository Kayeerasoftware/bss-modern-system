<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedImportedMigrationsHistory extends Command
{
    /**
     * The console command signature.
     *
     * This is used on Render to detect databases that were imported from the
     * SQL snapshot instead of being built from Laravel migrations.
     */
    private const BASELINE_CUTOFF = '2026_05_23_012216';

    protected $signature = 'deploy:seed-imported-migrations';

    protected $description = 'Seed Laravel migration history for a database imported from the production SQL snapshot.';

    public function handle(): int
    {
        if (! $this->looksLikeImportedSchema()) {
            return self::SUCCESS;
        }

        if (! Schema::hasTable('migrations')) {
            Schema::create('migrations', function (Blueprint $table): void {
                $table->id();
                $table->string('migration');
                $table->integer('batch');
            });
        }

        $migrationFiles = collect(glob(database_path('migrations/*.php')) ?: [])
            ->map(static fn (string $path): string => basename($path, '.php'))
            ->filter(static fn (string $migration): bool => $migration < self::BASELINE_CUTOFF)
            ->sort()
            ->values();

        if ($migrationFiles->isEmpty()) {
            $this->warn('No migration files were found to seed.');
            return self::SUCCESS;
        }

        $existing = Schema::hasTable('migrations')
            ? DB::table('migrations')->pluck('migration')->all()
            : [];

        $missingMigrations = $migrationFiles->reject(
            static fn (string $migration): bool => in_array($migration, $existing, true)
        )->values();

        if ($missingMigrations->isEmpty()) {
            return self::SUCCESS;
        }

        DB::table('migrations')->insert(
            $missingMigrations->map(static fn (string $migration): array => [
                'migration' => $migration,
                'batch' => 1,
            ])->all()
        );

        $this->info('Seeded baseline Laravel migration history for the imported Render database.');

        return self::SUCCESS;
    }

    private function looksLikeImportedSchema(): bool
    {
        $requiredTables = [
            'roles',
            'users',
            'members',
            'transactions',
            'savings_accounts',
        ];

        foreach ($requiredTables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }
}
