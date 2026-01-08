<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use App\Models\Deposit;
use App\Models\Meeting;
use App\Models\Document;
use App\Models\Share;
use App\Models\SavingsHistory;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;

class FinalSeeder extends Seeder
{
    public function run(): void
    {
        // Users (schema: name, email, password, role, is_active)
        $users = [
            ['name' => 'Admin User', 'email' => 'admin@bss.com', 'password' => Hash::make('admin123'), 'role' => 'admin', 'is_active' => true],
            ['name' => 'Manager User', 'email' => 'manager@bss.com', 'password' => Hash::make('manager123'), 'role' => 'manager', 'is_active' => true],
            ['name' => 'Treasurer User', 'email' => 'treasurer@bss.com', 'password' => Hash::make('treasurer123'), 'role' => 'treasurer', 'is_active' => true],
            ['name' => 'Member User', 'email' => 'member@bss.com', 'password' => Hash::make('member123'), 'role' => 'member', 'is_active' => true],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(['email' => $userData['email']], $userData);
        }

        // Members (schema: member_id, full_name, email, location, occupation, contact, savings, loan, balance, savings_balance, role, password)
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

        // Loans (schema: loan_id, member_id, amount, purpose, repayment_months, interest, monthly_payment, status)
        $loans = [
            ['loan_id' => 'LOAN001', 'member_id' => 'BSS002', 'amount' => 200000, 'purpose' => 'Business expansion', 'repayment_months' => 12, 'interest' => 10000, 'monthly_payment' => 17500, 'status' => 'approved'],
            ['loan_id' => 'LOAN002', 'member_id' => 'BSS003', 'amount' => 500000, 'purpose' => 'Home improvement', 'repayment_months' => 24, 'interest' => 25000, 'monthly_payment' => 21875, 'status' => 'approved'],
            ['loan_id' => 'LOAN003', 'member_id' => 'BSS005', 'amount' => 1000000, 'purpose' => 'Equipment purchase', 'repayment_months' => 24, 'interest' => 50000, 'monthly_payment' => 43750, 'status' => 'pending'],
        ];

        foreach ($loans as $loanData) {
            Loan::create($loanData);
        }

        // Transactions (schema: transaction_id, member_id, amount, type)
        $transactions = [
            ['transaction_id' => 'TXN001', 'member_id' => 'BSS001', 'amount' => 100000, 'type' => 'deposit'],
            ['transaction_id' => 'TXN002', 'member_id' => 'BSS002', 'amount' => 150000, 'type' => 'deposit'],
            ['transaction_id' => 'TXN003', 'member_id' => 'BSS003', 'amount' => 200000, 'type' => 'deposit'],
        ];

        foreach ($transactions as $transactionData) {
            Transaction::create($transactionData);
        }

        // Projects (schema: project_id, name, budget, timeline, description, progress, roi, risk_score)
        $projects = [
            ['project_id' => 'PRJ001', 'name' => 'Community Water Project', 'budget' => 5000000, 'timeline' => '2024-12-31', 'description' => 'Installing clean water systems', 'progress' => 65, 'roi' => 12.5, 'risk_score' => 30],
            ['project_id' => 'PRJ002', 'name' => 'Education Support Program', 'budget' => 3000000, 'timeline' => '2024-06-30', 'description' => 'Providing scholarships', 'progress' => 100, 'roi' => 8.0, 'risk_score' => 20],
            ['project_id' => 'PRJ003', 'name' => 'Healthcare Initiative', 'budget' => 8000000, 'timeline' => '2025-03-31', 'description' => 'Mobile health clinics', 'progress' => 15, 'roi' => 15.0, 'risk_score' => 40],
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }

        // Deposits (schema: first_sn, name, last_name, phone_number, account_number, deposit, amount, email)
        $deposits = [
            ['first_sn' => '001', 'name' => 'John', 'last_name' => 'Doe', 'phone_number' => '+256700123456', 'account_number' => 'BSS001', 'deposit' => 'savings', 'amount' => 100000, 'email' => 'john@bss.com'],
            ['first_sn' => '002', 'name' => 'Jane', 'last_name' => 'Smith', 'phone_number' => '+256700234567', 'account_number' => 'BSS002', 'deposit' => 'shares', 'amount' => 150000, 'email' => 'jane@bss.com'],
        ];

        foreach ($deposits as $depositData) {
            Deposit::create($depositData);
        }

        // Meetings (schema: title, description, scheduled_at, location, status, attendees, minutes, created_by)
        $meetings = [
            ['title' => 'Monthly General Meeting', 'description' => 'Regular monthly meeting', 'scheduled_at' => now()->addDays(7), 'location' => 'Community Center', 'status' => 'scheduled', 'attendees' => null, 'minutes' => null, 'created_by' => 'BSS005'],
            ['title' => 'Project Review Meeting', 'description' => 'Review project progress', 'scheduled_at' => now()->addDays(14), 'location' => 'BSS Office', 'status' => 'scheduled', 'attendees' => null, 'minutes' => null, 'created_by' => 'BSS004'],
        ];

        foreach ($meetings as $meetingData) {
            Meeting::create($meetingData);
        }

        // Documents (schema: title, filename, file_path, file_type, file_size, category, description, uploaded_by, access_roles)
        $documents = [
            ['title' => 'Group Constitution', 'filename' => 'constitution.pdf', 'file_path' => '/documents/constitution.pdf', 'file_type' => 'pdf', 'file_size' => 1024000, 'category' => 'legal', 'description' => 'Official constitution', 'uploaded_by' => 'BSS005', 'access_roles' => json_encode(['admin', 'manager'])],
            ['title' => 'Financial Report Q1', 'filename' => 'report_q1.pdf', 'file_path' => '/documents/report_q1.pdf', 'file_type' => 'pdf', 'file_size' => 512000, 'category' => 'financial', 'description' => 'Quarterly report', 'uploaded_by' => 'BSS004', 'access_roles' => json_encode(['admin', 'treasurer'])],
        ];

        foreach ($documents as $documentData) {
            Document::create($documentData);
        }

        // Notifications (schema: title, message, roles, type, is_read, created_by)
        $notifications = [
            ['title' => 'Welcome to BSS System', 'message' => 'Welcome to the BSS Investment Group system!', 'roles' => json_encode(['client', 'shareholder', 'cashier', 'td', 'ceo']), 'type' => 'info', 'is_read' => false, 'created_by' => 'system'],
            ['title' => 'New Loan Application', 'message' => 'A new loan application has been submitted', 'roles' => json_encode(['cashier', 'manager', 'admin']), 'type' => 'info', 'is_read' => false, 'created_by' => 'BSS005'],
        ];

        foreach ($notifications as $notificationData) {
            Notification::create($notificationData);
        }

        // Shares (schema: member_id, shares_owned, share_value, total_value, purchase_date, status)
        $shares = [
            ['member_id' => 'BSS002', 'shares_owned' => 1250, 'share_value' => 2000, 'total_value' => 2500000, 'purchase_date' => '2023-01-15', 'status' => 'active'],
            ['member_id' => 'BSS004', 'shares_owned' => 800, 'share_value' => 2000, 'total_value' => 1600000, 'purchase_date' => '2023-03-20', 'status' => 'active'],
            ['member_id' => 'BSS005', 'shares_owned' => 2000, 'share_value' => 2000, 'total_value' => 4000000, 'purchase_date' => '2023-02-10', 'status' => 'active'],
        ];

        foreach ($shares as $shareData) {
            Share::create($shareData);
        }

        // Savings History (schema: member_id, amount, type, balance_before, balance_after, description, transaction_date)
        $savingsHistory = [
            ['member_id' => 'BSS001', 'amount' => 100000, 'type' => 'deposit', 'balance_before' => 400000, 'balance_after' => 500000, 'description' => 'Monthly savings', 'transaction_date' => '2024-01-15'],
            ['member_id' => 'BSS001', 'amount' => 50000, 'type' => 'deposit', 'balance_before' => 350000, 'balance_after' => 400000, 'description' => 'Bonus deposit', 'transaction_date' => '2023-12-15'],
            ['member_id' => 'BSS001', 'amount' => 75000, 'type' => 'deposit', 'balance_before' => 275000, 'balance_after' => 350000, 'description' => 'Regular savings', 'transaction_date' => '2023-11-15'],
            ['member_id' => 'BSS002', 'amount' => 150000, 'type' => 'deposit', 'balance_before' => 600000, 'balance_after' => 750000, 'description' => 'Monthly savings', 'transaction_date' => '2024-01-10'],
            ['member_id' => 'BSS002', 'amount' => 100000, 'type' => 'deposit', 'balance_before' => 500000, 'balance_after' => 600000, 'description' => 'Salary deposit', 'transaction_date' => '2023-12-10'],
            ['member_id' => 'BSS003', 'amount' => 200000, 'type' => 'deposit', 'balance_before' => 1000000, 'balance_after' => 1200000, 'description' => 'Salary deposit', 'transaction_date' => '2024-01-05'],
        ];

        foreach ($savingsHistory as $historyData) {
            SavingsHistory::create($historyData);
        }

        // More transactions for better data
        $moreTransactions = [
            ['transaction_id' => 'TXN004', 'member_id' => 'BSS001', 'amount' => 25000, 'type' => 'withdrawal'],
            ['transaction_id' => 'TXN005', 'member_id' => 'BSS002', 'amount' => 50000, 'type' => 'transfer'],
            ['transaction_id' => 'TXN006', 'member_id' => 'BSS003', 'amount' => 75000, 'type' => 'deposit'],
            ['transaction_id' => 'TXN007', 'member_id' => 'BSS004', 'amount' => 100000, 'type' => 'deposit'],
            ['transaction_id' => 'TXN008', 'member_id' => 'BSS005', 'amount' => 200000, 'type' => 'deposit'],
        ];

        foreach ($moreTransactions as $transactionData) {
            Transaction::create($transactionData);
        }

        // Enhanced Projects
        $enhancedProjects = [
            ['project_id' => 'PRJ004', 'name' => 'Digital Banking Platform', 'budget' => 12000000, 'timeline' => '2024-09-30', 'description' => 'Mobile and web banking solution', 'progress' => 45, 'roi' => 25.0, 'risk_score' => 35],
            ['project_id' => 'PRJ005', 'name' => 'Microfinance Expansion', 'budget' => 6000000, 'timeline' => '2024-08-15', 'description' => 'Expanding microfinance services', 'progress' => 80, 'roi' => 18.5, 'risk_score' => 25],
        ];

        foreach ($enhancedProjects as $projectData) {
            Project::create($projectData);
        }

        // Create dividend records
        $dividends = [
            ['member_id' => 'BSS002', 'amount' => 125000, 'payment_date' => '2023-12-31', 'dividend_rate' => 5.0, 'shares_eligible' => 1250, 'status' => 'paid'],
            ['member_id' => 'BSS004', 'amount' => 80000, 'payment_date' => '2023-12-31', 'dividend_rate' => 5.0, 'shares_eligible' => 800, 'status' => 'paid'],
            ['member_id' => 'BSS005', 'amount' => 200000, 'payment_date' => '2023-12-31', 'dividend_rate' => 5.0, 'shares_eligible' => 2000, 'status' => 'paid'],
        ];

        foreach ($dividends as $dividendData) {
            \App\Models\Dividend::create($dividendData);
        }
    }
}