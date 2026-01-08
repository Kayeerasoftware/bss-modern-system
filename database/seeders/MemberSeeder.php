<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use App\Models\Deposit;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    public function run()
    {
        $members = [
            ['member_id' => 'BSS001', 'full_name' => 'John Doe', 'email' => 'john@bss.com', 'location' => 'Kampala', 'occupation' => 'Teacher', 'contact' => '+256700123456', 'savings' => 500000, 'loan' => 0, 'balance' => 500000, 'savings_balance' => 500000, 'role' => 'client', 'password' => Hash::make('password123')],
            ['member_id' => 'BSS002', 'full_name' => 'Jane Smith', 'email' => 'jane@bss.com', 'location' => 'Entebbe', 'occupation' => 'Nurse', 'contact' => '+256700234567', 'savings' => 750000, 'loan' => 200000, 'balance' => 750000, 'savings_balance' => 750000, 'role' => 'shareholder', 'password' => Hash::make('password123')],
            ['member_id' => 'BSS003', 'full_name' => 'Robert Johnson', 'email' => 'robert@bss.com', 'location' => 'Jinja', 'occupation' => 'Engineer', 'contact' => '+256700345678', 'savings' => 1200000, 'loan' => 500000, 'balance' => 1200000, 'savings_balance' => 1200000, 'role' => 'cashier', 'password' => Hash::make('password123')],
            ['member_id' => 'BSS004', 'full_name' => 'Mary Wilson', 'email' => 'mary@bss.com', 'location' => 'Mbarara', 'occupation' => 'Doctor', 'contact' => '+256700456789', 'savings' => 2000000, 'loan' => 0, 'balance' => 2000000, 'savings_balance' => 2000000, 'role' => 'td', 'password' => Hash::make('password123')],
            ['member_id' => 'BSS005', 'full_name' => 'David Brown', 'email' => 'david@bss.com', 'location' => 'Gulu', 'occupation' => 'Business Owner', 'contact' => '+256700567890', 'savings' => 3000000, 'loan' => 1000000, 'balance' => 3000000, 'savings_balance' => 3000000, 'role' => 'ceo', 'password' => Hash::make('password123')],
        ];

        foreach ($members as $memberData) {
            Member::firstOrCreate(['member_id' => $memberData['member_id']], $memberData);
        }

        $loans = [
            ['member_id' => 'BSS002', 'amount' => 200000, 'interest_rate' => 5, 'duration_months' => 12, 'purpose' => 'Business expansion', 'status' => 'active', 'amount_paid' => 50000, 'application_date' => now()->subMonths(2), 'approved_date' => now()->subMonths(2)->addDays(3)],
            ['member_id' => 'BSS003', 'amount' => 500000, 'interest_rate' => 5, 'duration_months' => 24, 'purpose' => 'Home improvement', 'status' => 'active', 'amount_paid' => 125000, 'application_date' => now()->subMonths(3), 'approved_date' => now()->subMonths(3)->addDays(2)],
            ['member_id' => 'BSS005', 'amount' => 1000000, 'interest_rate' => 5, 'duration_months' => 24, 'purpose' => 'Equipment purchase', 'status' => 'pending', 'amount_paid' => 0, 'application_date' => now()->subDays(5)],
        ];

        foreach ($loans as $loanData) {
            Loan::create($loanData);
        }

        $transactions = [
            ['member_id' => 'BSS001', 'amount' => 100000, 'type' => 'deposit', 'description' => 'Monthly savings', 'reference' => 'TXN001', 'status' => 'completed'],
            ['member_id' => 'BSS002', 'amount' => 150000, 'type' => 'deposit', 'description' => 'Quarterly savings', 'reference' => 'TXN002', 'status' => 'completed'],
            ['member_id' => 'BSS003', 'amount' => 200000, 'type' => 'deposit', 'description' => 'Annual savings', 'reference' => 'TXN003', 'status' => 'completed'],
        ];

        foreach ($transactions as $transactionData) {
            Transaction::create($transactionData);
        }

        $projects = [
            ['name' => 'Community Water Project', 'description' => 'Installing clean water systems', 'budget' => 5000000, 'start_date' => '2024-01-01', 'end_date' => '2024-12-31', 'status' => 'in_progress', 'progress' => 65],
            ['name' => 'Education Support Program', 'description' => 'Providing scholarships', 'budget' => 3000000, 'start_date' => '2024-03-01', 'end_date' => '2024-06-30', 'status' => 'completed', 'progress' => 100],
            ['name' => 'Healthcare Initiative', 'description' => 'Mobile health clinics', 'budget' => 8000000, 'start_date' => '2024-06-01', 'end_date' => '2025-03-31', 'status' => 'planning', 'progress' => 15],
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }

        $deposits = [
            ['member_name' => 'John Doe', 'member_id' => 'BSS001', 'amount' => 100000, 'deposit_type' => 'savings', 'payment_method' => 'cash', 'description' => 'Monthly deposit'],
            ['member_name' => 'Jane Smith', 'member_id' => 'BSS002', 'amount' => 150000, 'deposit_type' => 'shares', 'payment_method' => 'mobile_money', 'description' => 'Share purchase'],
        ];

        foreach ($deposits as $depositData) {
            Deposit::create($depositData);
        }
    }
}