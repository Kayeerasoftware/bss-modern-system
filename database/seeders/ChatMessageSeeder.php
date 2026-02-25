<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatMessage;
use App\Models\Member;

class ChatMessageSeeder extends Seeder
{
    public function run()
    {
        $members = Member::limit(10)->get();
        
        if ($members->count() < 2) {
            return;
        }

        $messages = [
            'Hello, how are you?',
            'Can we discuss the loan application?',
            'Thank you for your help!',
            'When is the next meeting?',
            'I need assistance with my account',
            'Great work on the project!',
            'Please review my documents',
            'What time should I come?',
            'Thanks for the update',
            'See you tomorrow'
        ];

        foreach ($members as $index => $member) {
            if ($index > 0) {
                ChatMessage::create([
                    'sender_id' => $members[0]->member_id,
                    'receiver_id' => $member->member_id,
                    'message' => $messages[array_rand($messages)],
                    'is_read' => rand(0, 1),
                    'created_at' => now()->subMinutes(rand(1, 1440))
                ]);

                ChatMessage::create([
                    'sender_id' => $member->member_id,
                    'receiver_id' => $members[0]->member_id,
                    'message' => $messages[array_rand($messages)],
                    'is_read' => rand(0, 1),
                    'created_at' => now()->subMinutes(rand(1, 1440))
                ]);
            }
        }
    }
}
