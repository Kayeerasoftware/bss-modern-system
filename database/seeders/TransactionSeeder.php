<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $members = Member::pluck('member_id')->toArray();
        
        if (empty($members)) {
            $this->command->info('No members found. Please run MemberSeeder first.');
            return;
        }

        $types = ['deposit', 'withdrawal', 'transfer', 'loan_payment', 'loan_request', 'fundraising', 'condolence'];
        $descriptions = [
            'deposit' => ['Monthly savings', 'Salary deposit', 'Business income', 'Investment return'],
            'withdrawal' => ['Emergency withdrawal', 'School fees', 'Medical expenses', 'Business capital'],
            'transfer' => ['Transfer to member', 'Internal transfer', 'Account transfer'],
            'loan_payment' => ['Loan repayment', 'Monthly installment', 'Loan settlement'],
            'loan_request' => ['Business loan request', 'Emergency loan', 'Education loan', 'Medical loan'],
            'fundraising' => ['Community project', 'School fundraising', 'Church fundraising', 'Medical fundraising'],
            'condolence' => ['Funeral support', 'Bereavement fund', 'Condolence contribution']
        ];

        $transactions = [];
        
        for ($i = 0; $i < 50; $i++) {
            $type = $types[array_rand($types)];
            $amount = rand(10000, 500000);
            
            $transactions[] = [
                'transaction_id' => 'TXN' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'member_id' => $members[array_rand($members)],
                'type' => $type,
                'amount' => $amount,
                'description' => $descriptions[$type][array_rand($descriptions[$type])],
                'created_at' => now()->subDays(rand(0, 90)),
                'updated_at' => now()
            ];
        }

        DB::table('transactions')->insert($transactions);
        
        $this->command->info('50 transactions created successfully!');
    }
}
