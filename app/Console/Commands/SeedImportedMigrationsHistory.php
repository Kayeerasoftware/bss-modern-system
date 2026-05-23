<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedImportedMigrationsHistory extends Command
{
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
            ->sort()
            ->values();

        if ($migrationFiles->isEmpty()) {
            $this->warn('No migration files were found to seed.');
            return self::SUCCESS;
        }

        $existing = Schema::hasTable('migrations')
            ? DB::table('migrations')->pluck('migration')->all()
            : [];

        $missingMigrations = $migrationFiles
            ->filter(function (string $migration): bool {
                $tableName = $this->createdTableName($migration);
                return $tableName !== null && Schema::hasTable($tableName);
            })
            ->reject(static fn (string $migration): bool => in_array($migration, $existing, true))
            ->values();

        if ($missingMigrations->isEmpty()) {
            return self::SUCCESS;
        }

        DB::table('migrations')->insert(
            $missingMigrations->map(static fn (string $migration): array => [
                'migration' => $migration,
                'batch' => 1,
            ])->all()
        );

        $this->info('Seeded existing create-table migrations for the imported Render database.');

        return self::SUCCESS;
    }

    private function looksLikeImportedSchema(): bool
    {
        $requiredTables = [
            'roles',
            'users',
            'members',
            'transactions',
        ];

        foreach ($requiredTables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    private function createdTableName(string $migration): ?string
    {
        if (preg_match('/^\d+_create_([a-z0-9_]+)_table$/', $migration, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }
}
