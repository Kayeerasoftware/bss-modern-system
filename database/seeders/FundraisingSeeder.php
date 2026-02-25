<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fundraising;

class FundraisingSeeder extends Seeder
{
    public function run(): void
    {
        $campaigns = [
            ['title' => 'Community Water Project', 'description' => 'Building a clean water well for the community', 'target' => 10000000, 'raised' => 7500000],
            ['title' => 'School Building Fund', 'description' => 'Construction of new classrooms for primary school', 'target' => 25000000, 'raised' => 15000000],
            ['title' => 'Medical Equipment Drive', 'description' => 'Purchase medical equipment for local health center', 'target' => 15000000, 'raised' => 12000000],
            ['title' => 'Youth Skills Training', 'description' => 'Vocational training program for unemployed youth', 'target' => 8000000, 'raised' => 8000000],
            ['title' => 'Agricultural Support', 'description' => 'Providing farming tools and seeds to farmers', 'target' => 12000000, 'raised' => 5000000],
        ];

        foreach ($campaigns as $index => $campaign) {
            Fundraising::create([
                'campaign_id' => 'FND' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                'title' => $campaign['title'],
                'description' => $campaign['description'],
                'target_amount' => $campaign['target'],
                'raised_amount' => $campaign['raised'],
                'start_date' => now()->subDays(rand(30, 90)),
                'end_date' => now()->addDays(rand(30, 90)),
                'status' => $campaign['raised'] >= $campaign['target'] ? 'completed' : 'active',
            ]);
        }
    }
}

