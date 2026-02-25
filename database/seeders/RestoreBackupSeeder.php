<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestoreBackupSeeder extends Seeder
{
    public function run()
    {
        $backupFile = storage_path('app/backups/backup_2026-01-11_174913.json');
        
        if (!file_exists($backupFile)) {
            $this->command->error("Backup file not found: $backupFile");
            return;
        }
        
        $this->command->info("Loading backup file...");
        $data = json_decode(file_get_contents($backupFile), true);
        
        if (!$data) {
            $this->command->error("Failed to parse backup file");
            return;
        }
        
        $this->command->info("Disabling foreign key checks...");
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $totalRecords = 0;
        
        foreach ($data as $table => $records) {
            if (empty($records) || $table === 'migrations') {
                continue;
            }
            
            try {
                $this->command->info("Restoring table: $table");
                
                // Truncate table
                DB::table($table)->truncate();
                
                // Insert records in chunks
                $chunks = array_chunk($records, 100);
                foreach ($chunks as $chunk) {
                    DB::table($table)->insert($chunk);
                }
                
                $count = count($records);
                $totalRecords += $count;
                $this->command->info("  ✓ Restored $count records");
                
            } catch (\Exception $e) {
                $this->command->warn("  ✗ Failed to restore $table: " . $e->getMessage());
            }
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info("\n" . str_repeat('=', 50));
        $this->command->info("✅ BACKUP RESTORED SUCCESSFULLY!");
        $this->command->info("Total records restored: $totalRecords");
        $this->command->info(str_repeat('=', 50));
    }
}
