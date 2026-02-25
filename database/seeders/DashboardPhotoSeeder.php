<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DashboardPhoto;

class DashboardPhotoSeeder extends Seeder
{
    public function run(): void
    {
        // Project Photos
        DashboardPhoto::create([
            'type' => 'project',
            'photo_path' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=400&h=300&fit=crop',
            'title' => 'Financial Planning',
            'order' => 1,
            'is_active' => true
        ]);

        DashboardPhoto::create([
            'type' => 'project',
            'photo_path' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&h=300&fit=crop',
            'title' => 'Data Analytics',
            'order' => 2,
            'is_active' => true
        ]);

        DashboardPhoto::create([
            'type' => 'project',
            'photo_path' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=400&h=300&fit=crop',
            'title' => 'Team Collaboration',
            'order' => 3,
            'is_active' => true
        ]);

        DashboardPhoto::create([
            'type' => 'project',
            'photo_path' => 'https://images.unsplash.com/photo-1553877522-43269d4ea984?w=400&h=300&fit=crop',
            'title' => 'Business Growth',
            'order' => 4,
            'is_active' => true
        ]);

        // Meeting Photos
        DashboardPhoto::create([
            'type' => 'meeting',
            'photo_path' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=400&h=300&fit=crop',
            'title' => 'Board Meeting',
            'order' => 1,
            'is_active' => true
        ]);

        DashboardPhoto::create([
            'type' => 'meeting',
            'photo_path' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=400&h=300&fit=crop',
            'title' => 'Conference Room',
            'order' => 2,
            'is_active' => true
        ]);

        DashboardPhoto::create([
            'type' => 'meeting',
            'photo_path' => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=400&h=300&fit=crop',
            'title' => 'Team Discussion',
            'order' => 3,
            'is_active' => true
        ]);

        DashboardPhoto::create([
            'type' => 'meeting',
            'photo_path' => 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=400&h=300&fit=crop',
            'title' => 'Strategy Session',
            'order' => 4,
            'is_active' => true
        ]);
    }
}
