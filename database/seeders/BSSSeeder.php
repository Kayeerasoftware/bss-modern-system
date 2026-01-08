<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use App\Models\Deposit;
use App\Models\Share;
use App\Models\Dividend;
use App\Models\SavingsHistory;
use Illuminate\Support\Facades\Hash;

class BSSSeeder extends Seeder
{
    public function run(): void
    {
        // Create Users with different roles
        $users = [
            ['name' => 'Admin User', 'email' => 'admin@bss.com', 'password' => Hash::make('admin123'), 'role' => 'admin'],
            ['name' => 'Manager User', 'email' => 'manager@bss.com', 'password' => Hash::make('manager123'), 'role' => 'manager'],
            ['name' => 'Treasurer User', 'email' => 'treasurer@bss.com', 'password' => Hash::make('treasurer123'), 'role' => 'treasurer'],
            ['name' => 'Secretary User', 'email' => 'secretary@bss.com', 'password' => Hash::make('secretary123'), 'role' => 'secretary'],
            ['name' => 'Member User', 'email' => 'member@bss.com', 'password' => Hash::make('member123'), 'role' => 'member'],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Create Members
        $members = [
            ['member_id' => 'BSS001', 'full_name' => 'John Doe', 'email' => 'john@bss.com', 'location' => 'Kampala', 'occupation' => 'Teacher', 'contact' => '+256700123456', 'savings' => 500000, 'loan' => 0, 'balance' => 750000, 'savings_balance' => 500000, 'role' => 'client', 'password' => Hash::make('password123')],
            ['member_id' => 'BSS002', 'full_name' => 'Jane Smith', 'email' => 'jane@bss.com', 'location' => 'Entebbe', 'occupation' => 'Nurse', 'contact' => '+256700234567', 'savings' => 750000, 'loan' => 200000, 'balance' => 950000, 'savings_balance' => 750000, 'role' => 'shareholder', 'password' => Hash::make('password123')],
            ['member_id' => 'BSS003', 'full_name' => 'Robert Johnson', 'email' => 'robert@bss.com', 'location' => 'Jinja', 'occupation' => 'Engineer', 'contact' => '+256700345678', 'savings' => 1200000, 'loan' => 500000, 'balance' => 1400000, 'savings_balance' => 1200000, 'role' => 'cashier', 'password' => Hash::make('password123')],
            ['member_id' => 'BSS004', 'full_name' => 'Mary Wilson', 'email' => 'mary@bss.com', 'location' => 'Mbarara', 'occupation' => 'Doctor', 'contact' => '+256700456789', 'savings' => 2000000, 'loan' => 0, 'balance' => 2200000, 'savings_balance' => 2000000, 'role' => 'td', 'password' => Hash::make('password123')],
            ['member_id' => 'BSS005', 'full_name' => 'David Brown', 'email' => 'david@bss.com', 'location' => 'Gulu', 'occupation' => 'Business Owner', 'contact' => '+256700567890', 'savings' => 3000000, 'loan' => 1000000, 'balance' => 3500000, 'savings_balance' => 3000000, 'role' => 'ceo', 'password' => Hash::make('password123')],
            ['member_id' => 'BSS006', 'full_name' => 'Sarah Connor', 'email' => 'sarah@bss.com', 'location' => 'Masaka', 'occupation' => 'Accountant', 'contact' => '+256700678901', 'savings' => 800000, 'loan' => 0, 'balance' => 850000, 'savings_balance' => 800000, 'role' => 'client', 'password' => Hash::make('password123')],
            ['member_id' => 'BSS007', 'full_name' => 'Michael Davis', 'email' => 'michael@bss.com', 'location' => 'Mbale', 'occupation' => 'Farmer', 'contact' => '+256700789012', 'savings' => 600000, 'loan' => 300000, 'balance' => 700000, 'savings_balance' => 600000, 'role' => 'client', 'password' => Hash::make('password123')],
            ['member_id' => 'BSS008', 'full_name' => 'Lisa Anderson', 'email' => 'lisa@bss.com', 'location' => 'Fort Portal', 'occupation' => 'Lawyer', 'contact' => '+256700890123', 'savings' => 1500000, 'loan' => 0, 'balance' => 1600000, 'savings_balance' => 1500000, 'role' => 'shareholder', 'password' => Hash::make('password123')],
        ];

        foreach ($members as $memberData) {
            Member::create($memberData);
        }

        // Create Deposits
        $deposits = [
            ['member_name' => 'John Doe', 'member_id' => 'BSS001', 'amount' => 100000, 'deposit_type' => 'savings', 'payment_method' => 'cash', 'description' => 'Monthly savings deposit'],
            ['member_name' => 'Jane Smith', 'member_id' => 'BSS002', 'amount' => 150000, 'deposit_type' => 'shares', 'payment_method' => 'mobile_money', 'description' => 'Share purchase'],
            ['member_name' => 'Robert Johnson', 'member_id' => 'BSS003', 'amount' => 200000, 'deposit_type' => 'loan_repayment', 'payment_method' => 'bank_transfer', 'description' => 'Loan installment payment'],
            ['member_name' => 'Mary Wilson', 'member_id' => 'BSS004', 'amount' => 250000, 'deposit_type' => 'savings', 'payment_method' => 'cash', 'description' => 'Quarterly savings'],
        ];

        foreach ($deposits as $depositData) {
            Deposit::create($depositData);
        }

        // Create Loans
        $loans = [
            ['member_id' => 'BSS002', 'amount' => 200000, 'interest_rate' => 5, 'duration_months' => 12, 'purpose' => 'Small business expansion', 'status' => 'active', 'amount_paid' => 50000, 'application_date' => now()->subMonths(2), 'approved_date' => now()->subMonths(2)->addDays(3)],
            ['member_id' => 'BSS003', 'amount' => 500000, 'interest_rate' => 5, 'duration_months' => 24, 'purpose' => 'Home improvement', 'status' => 'active', 'amount_paid' => 125000, 'application_date' => now()->subMonths(3), 'approved_date' => now()->subMonths(3)->addDays(2)],
            ['member_id' => 'BSS005', 'amount' => 1000000, 'interest_rate' => 5, 'duration_months' => 24, 'purpose' => 'Equipment purchase', 'status' => 'pending', 'amount_paid' => 0, 'application_date' => now()->subDays(5)],
            ['member_id' => 'BSS007', 'amount' => 300000, 'interest_rate' => 5, 'duration_months' => 18, 'purpose' => 'Agricultural inputs', 'status' => 'active', 'amount_paid' => 75000, 'application_date' => now()->subMonths(1), 'approved_date' => now()->subMonths(1)->addDays(2)],
        ];

        foreach ($loans as $loanData) {
            Loan::create($loanData);
        }

        // Create Transactions
        $transactions = [
            ['member_id' => 'BSS001', 'amount' => 100000, 'type' => 'deposit', 'description' => 'Monthly savings deposit', 'reference' => 'TXN001', 'status' => 'completed'],
            ['member_id' => 'BSS002', 'amount' => 50000, 'type' => 'withdrawal', 'description' => 'Emergency withdrawal', 'reference' => 'TXN002', 'status' => 'completed'],
            ['member_id' => 'BSS003', 'amount' => 200000, 'type' => 'deposit', 'description' => 'Quarterly savings', 'reference' => 'TXN003', 'status' => 'completed'],
            ['member_id' => 'BSS004', 'amount' => 300000, 'type' => 'deposit', 'description' => 'Bonus savings', 'reference' => 'TXN004', 'status' => 'completed'],
            ['member_id' => 'BSS005', 'amount' => 150000, 'type' => 'deposit', 'description' => 'Business profit sharing', 'reference' => 'TXN005', 'status' => 'completed'],
            ['member_id' => 'BSS006', 'amount' => 75000, 'type' => 'deposit', 'description' => 'Regular savings', 'reference' => 'TXN006', 'status' => 'completed'],
            ['member_id' => 'BSS007', 'amount' => 120000, 'type' => 'deposit', 'description' => 'Harvest season savings', 'reference' => 'TXN007', 'status' => 'completed'],
            ['member_id' => 'BSS008', 'amount' => 180000, 'type' => 'deposit', 'description' => 'Professional fee savings', 'reference' => 'TXN008', 'status' => 'completed'],
        ];

        foreach ($transactions as $transactionData) {
            Transaction::create($transactionData);
        }

        // Create Projects
        $projects = [
            ['name' => 'Community Water Project', 'description' => 'Installing clean water systems in rural communities', 'budget' => 5000000, 'start_date' => '2024-01-01', 'end_date' => '2024-12-31', 'status' => 'in_progress', 'progress' => 65],
            ['name' => 'Education Support Program', 'description' => 'Providing scholarships and educational materials', 'budget' => 3000000, 'start_date' => '2024-03-01', 'end_date' => '2024-06-30', 'status' => 'completed', 'progress' => 100],
            ['name' => 'Healthcare Initiative', 'description' => 'Mobile health clinics for remote areas', 'budget' => 8000000, 'start_date' => '2024-06-01', 'end_date' => '2025-03-31', 'status' => 'planning', 'progress' => 15],
            ['name' => 'Agricultural Development', 'description' => 'Supporting local farmers with modern techniques', 'budget' => 4500000, 'start_date' => '2024-02-15', 'end_date' => '2024-11-30', 'status' => 'in_progress', 'progress' => 40],
            ['name' => 'Youth Skills Training', 'description' => 'Vocational training for unemployed youth', 'budget' => 2500000, 'start_date' => '2024-04-01', 'end_date' => '2024-09-30', 'status' => 'in_progress', 'progress' => 55],
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }

        // Create more transactions for better chart data (last 6 months)
        $additionalTransactions = [];
        for ($month = 5; $month >= 0; $month--) {
            foreach ($members as $index => $memberData) {
                $date = now()->subMonths($month)->addDays(rand(1, 28));
                $additionalTransactions[] = [
                    'member_id' => $memberData['member_id'],
                    'amount' => rand(50000, 300000),
                    'type' => rand(0, 10) > 2 ? 'deposit' : 'withdrawal',
                    'description' => 'Monthly transaction',
                    'reference' => 'TXN' . (1000 + ($month * 10) + $index),
                    'status' => 'completed',
                    'created_at' => $date,
                    'updated_at' => $date
                ];
            }
        }

        foreach ($additionalTransactions as $transactionData) {
            Transaction::create($transactionData);
        }

        // Create Shares for shareholders
        $shareData = [
            ['member_id' => 'BSS002', 'shares_owned' => 50, 'share_value' => 10000, 'total_value' => 500000, 'purchase_date' => now()->subMonths(6), 'status' => 'active'],
            ['member_id' => 'BSS005', 'shares_owned' => 100, 'share_value' => 10000, 'total_value' => 1000000, 'purchase_date' => now()->subMonths(12), 'status' => 'active'],
            ['member_id' => 'BSS008', 'shares_owned' => 75, 'share_value' => 10000, 'total_value' => 750000, 'purchase_date' => now()->subMonths(8), 'status' => 'active'],
        ];

        foreach ($shareData as $share) {
            Share::create($share);
        }

        // Create Dividends
        $dividendData = [
            ['member_id' => 'BSS002', 'amount' => 25000, 'year' => 2024, 'quarter' => 1, 'status' => 'paid', 'payment_date' => now()->subMonths(3)],
            ['member_id' => 'BSS002', 'amount' => 30000, 'year' => 2024, 'quarter' => 2, 'status' => 'paid', 'payment_date' => now()->subMonths(1)],
            ['member_id' => 'BSS005', 'amount' => 50000, 'year' => 2024, 'quarter' => 1, 'status' => 'paid', 'payment_date' => now()->subMonths(3)],
            ['member_id' => 'BSS005', 'amount' => 60000, 'year' => 2024, 'quarter' => 2, 'status' => 'paid', 'payment_date' => now()->subMonths(1)],
            ['member_id' => 'BSS008', 'amount' => 37500, 'year' => 2024, 'quarter' => 1, 'status' => 'paid', 'payment_date' => now()->subMonths(3)],
            ['member_id' => 'BSS008', 'amount' => 45000, 'year' => 2024, 'quarter' => 2, 'status' => 'paid', 'payment_date' => now()->subMonths(1)],
        ];

        foreach ($dividendData as $dividend) {
            Dividend::create($dividend);
        }

        // Create Savings History for members
        foreach ($members as $memberData) {
            $balance = 0;
            for ($i = 5; $i >= 0; $i--) {
                $amount = rand(50000, 200000);
                $balance += $amount;
                SavingsHistory::create([
                    'member_id' => $memberData['member_id'],
                    'amount' => $amount,
                    'type' => 'deposit',
                    'balance_before' => $balance - $amount,
                    'balance_after' => $balance,
                    'description' => 'Monthly savings deposit',
                    'transaction_date' => now()->subMonths($i),
                ]);
            }
        }
    }
}
            ['member_id' => 'BSS001', 'amount' => 50000, 'type' => 'withdrawal', 'description' => 'ATM withdrawal', 'reference' => 'TXN009', 'status' => 'completed', 'created_at' => now()->subMonths(1)],
            ['member_id' => 'BSS002', 'amount' => 200000, 'type' => 'deposit', 'description' => 'Salary deposit', 'reference' => 'TXN010', 'status' => 'completed', 'created_at' => now()->subMonths(2)],
            ['member_id' => 'BSS003', 'amount' => 300000, 'type' => 'deposit', 'description' => 'Business income', 'reference' => 'TXN011', 'status' => 'completed', 'created_at' => now()->subMonths(3)],
            ['member_id' => 'BSS004', 'amount' => 100000, 'type' => 'withdrawal', 'description' => 'Medical expenses', 'reference' => 'TXN012', 'status' => 'completed', 'created_at' => now()->subMonths(1)],
            ['member_id' => 'BSS005', 'amount' => 500000, 'type' => 'deposit', 'description' => 'Investment return', 'reference' => 'TXN013', 'status' => 'completed', 'created_at' => now()->subWeeks(2)],
            ['member_id' => 'BSS006', 'amount' => 80000, 'type' => 'deposit', 'description' => 'Monthly savings', 'reference' => 'TXN014', 'status' => 'completed', 'created_at' => now()->subWeeks(1)],
            ['member_id' => 'BSS007', 'amount' => 150000, 'type' => 'deposit', 'description' => 'Crop sales', 'reference' => 'TXN015', 'status' => 'completed', 'created_at' => now()->subDays(5)],
            ['member_id' => 'BSS008', 'amount' => 250000, 'type' => 'deposit', 'description' => 'Legal fees', 'reference' => 'TXN016', 'status' => 'completed', 'created_at' => now()->subDays(2)],
        ];

        foreach ($additionalTransactions as $transactionData) {
            Transaction::create($transactionData);
        }

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }
    }
}