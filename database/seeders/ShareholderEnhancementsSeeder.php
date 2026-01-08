<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShareholderEnhancementsSeeder extends Seeder
{
    public function run()
    {
        // Portfolio Performance Data
        $members = ['BSS001', 'BSS002', 'BSS003'];
        
        foreach ($members as $memberId) {
            for ($i = 11; $i >= 0; $i--) {
                DB::table('portfolio_performances')->insert([
                    'member_id' => $memberId,
                    'period' => Carbon::now()->subMonths($i)->startOfMonth(),
                    'portfolio_value' => 2000000 + ($i * 40000) + rand(-20000, 50000),
                    'market_value' => 1950000 + ($i * 35000) + rand(-15000, 40000),
                    'performance_percentage' => 8.5 + ($i * 0.3) + (rand(-10, 20) / 10),
                    'benchmark_comparison' => 2.0 + (rand(-5, 15) / 10),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // Investment Opportunities
        DB::table('investment_opportunities')->insert([
            [
                'title' => 'Renewable Energy Project',
                'description' => 'Solar power installation for rural communities with sustainable energy solutions',
                'target_amount' => 50000000,
                'minimum_investment' => 500000,
                'expected_roi' => 18.5,
                'risk_level' => 'medium',
                'launch_date' => Carbon::now()->subDays(10),
                'deadline' => Carbon::now()->addDays(20),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Tech Innovation Hub',
                'description' => 'Co-working space and startup incubator for technology entrepreneurs',
                'target_amount' => 75000000,
                'minimum_investment' => 1000000,
                'expected_roi' => 22.0,
                'risk_level' => 'high',
                'launch_date' => Carbon::now()->addDays(25),
                'deadline' => Carbon::now()->addDays(55),
                'status' => 'upcoming',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Agricultural Expansion',
                'description' => 'Modern farming equipment and training program for local farmers',
                'target_amount' => 30000000,
                'minimum_investment' => 300000,
                'expected_roi' => 14.5,
                'risk_level' => 'low',
                'launch_date' => Carbon::now()->subDays(5),
                'deadline' => Carbon::now()->addDays(15),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Update existing dividends with more details
        DB::table('dividends')->insert([
            [
                'member_id' => 'BSS001',
                'amount' => 135000,
                'payment_date' => Carbon::parse('2024-03-15'),
                'dividend_rate' => 10.5,
                'shares_eligible' => 1250,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'member_id' => 'BSS002',
                'amount' => 95000,
                'payment_date' => Carbon::parse('2024-03-15'),
                'dividend_rate' => 10.5,
                'shares_eligible' => 880,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
