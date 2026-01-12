<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $notifications = [
            [
                'title' => 'Welcome to BSS Investment Group',
                'message' => 'Thank you for joining BSS Investment Group. We are excited to have you as part of our community.',
                'roles' => json_encode(['client', 'shareholder', 'cashier', 'td', 'ceo']),
                'type' => 'success',
                'is_read' => false,
                'created_by' => 'admin',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'title' => 'Monthly Meeting Reminder',
                'message' => 'Our monthly general meeting is scheduled for next week. Please mark your calendars and ensure attendance.',
                'roles' => json_encode(['shareholder', 'ceo', 'td']),
                'type' => 'warning',
                'is_read' => false,
                'created_by' => 'admin',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'title' => 'Loan Payment Due',
                'message' => 'This is a reminder that your loan payment is due in 5 days. Please make arrangements to avoid late fees.',
                'roles' => json_encode(['client']),
                'type' => 'error',
                'is_read' => false,
                'created_by' => 'admin',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'title' => 'New Investment Opportunity',
                'message' => 'A new investment opportunity is now available. Check the projects section for more details.',
                'roles' => json_encode(['shareholder', 'ceo']),
                'type' => 'info',
                'is_read' => false,
                'created_by' => 'admin',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'title' => 'System Maintenance Notice',
                'message' => 'The system will undergo maintenance this weekend. Services may be temporarily unavailable.',
                'roles' => json_encode(['client', 'shareholder', 'cashier', 'td', 'ceo']),
                'type' => 'warning',
                'is_read' => false,
                'created_by' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($notifications as $notification) {
            DB::table('notifications')->insert($notification);
        }
    }
}
