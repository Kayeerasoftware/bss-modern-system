<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Member;
use App\Models\User;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $members = Member::all();
        $users = User::where('role', 'admin')->orWhere('role', 'cashier')->get();

        if ($members->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No members or users found. Please seed members and users first.');
            return;
        }

        $types = ['deposit', 'withdrawal', 'transfer'];
        $methods = ['cash', 'bank_transfer', 'mobile_money', 'cheque'];
        $statuses = ['completed', 'pending', 'failed'];

        foreach ($members->random(min(20, $members->count())) as $member) {
            $transactionCount = rand(3, 8);
            
            for ($i = 0; $i < $transactionCount; $i++) {
                $type = $types[array_rand($types)];
                $amount = rand(10000, 500000);
                
                Transaction::create([
                    'member_id' => $member->member_id,
                    'type' => $type,
                    'amount' => $amount,
                    'payment_method' => $methods[array_rand($methods)],
                    'reference' => 'TXN' . strtoupper(uniqid()),
                    'description' => $this->getDescription($type),
                    'status' => $statuses[array_rand($statuses)],
                    'processed_by' => $users->random()->id,
                    'transaction_date' => now()->subDays(rand(0, 90)),
                    'created_at' => now()->subDays(rand(0, 90)),
                    'updated_at' => now()->subDays(rand(0, 90)),
                ]);
            }
        }

        $this->command->info('Transactions seeded successfully!');
    }

    private function getDescription($type)
    {
        $descriptions = [
            'deposit' => [
                'Monthly savings contribution',
                'Initial deposit',
                'Additional savings',
                'Dividend reinvestment',
                'Bonus deposit',
            ],
            'withdrawal' => [
                'Emergency withdrawal',
                'Partial withdrawal',
                'Savings withdrawal',
                'Account closure withdrawal',
                'Loan repayment withdrawal',
            ],
            'transfer' => [
                'Internal transfer',
                'Account transfer',
                'Balance adjustment',
                'Fund reallocation',
                'Inter-account transfer',
            ],
        ];

        return $descriptions[$type][array_rand($descriptions[$type])];
    }
}
