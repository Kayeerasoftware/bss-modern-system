<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Member;

class LoanSeeder extends Seeder
{
    public function run()
    {
        $members = Member::limit(5)->get();
        
        if ($members->isEmpty()) {
            $this->command->info('No members found. Please seed members first.');
            return;
        }

        $loans = [];
        $statuses = ['pending', 'approved', 'rejected'];
        
        foreach ($members as $index => $member) {
            $amount = rand(500000, 5000000);
            $months = rand(6, 36);
            $interest = $amount * 0.05;
            $monthlyPayment = ($amount + $interest) / $months;
            
            $loans[] = [
                'loan_id' => 'LOAN' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'member_id' => $member->member_id,
                'amount' => $amount,
                'purpose' => ['Business expansion', 'Education', 'Home improvement', 'Medical expenses', 'Investment'][$index % 5],
                'repayment_months' => $months,
                'interest' => $interest,
                'monthly_payment' => $monthlyPayment,
                'status' => $statuses[$index % 3],
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ];
        }

        DB::table('loans')->insert($loans);
        $this->command->info('Loans seeded successfully!');
    }
}
