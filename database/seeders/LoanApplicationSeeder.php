<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Loans\LoanApplication;
use App\Models\Member;

class LoanApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $members = Member::all();
        $purposes = ['Business expansion', 'Education fees', 'Medical expenses', 'Home renovation', 'Agriculture', 'Emergency funds'];
        $statuses = ['pending', 'approved', 'rejected'];

        foreach ($members->take(15) as $index => $member) {
            LoanApplication::create([
                'application_id' => 'APP' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                'member_id' => $member->member_id,
                'amount' => rand(500000, 5000000),
                'purpose' => $purposes[array_rand($purposes)],
                'repayment_months' => [6, 12, 18, 24, 36][array_rand([6, 12, 18, 24, 36])],
                'interest_rate' => rand(8, 15),
                'status' => $statuses[array_rand($statuses)],
                'reviewed_by' => rand(0, 1) ? 1 : null,
                'reviewed_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                'rejection_reason' => rand(0, 1) ? 'Insufficient credit history' : null,
            ]);
        }
    }
}

