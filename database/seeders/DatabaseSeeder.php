<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Core system data
            ComprehensiveUgandaLocationsSeeder::class,
            SystemSettingsSeeder::class,
            PermissionSeeder::class,
            
            // User and member data (this creates users and members)
            UserMemberSeeder::class,
            
            // Additional business data
            TransactionSeeder::class,
            LoanSeeder::class,
            
            // Complete all remaining tables
            CompleteDataSeeder::class,
            
            // Notifications
            NotificationSeeder::class,
        ]);
    }
}