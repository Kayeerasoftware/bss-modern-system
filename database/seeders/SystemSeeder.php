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
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $users = [
            ['name' => 'Admin User', 'email' => 'admin@bss.com', 'password' => Hash::make('admin123'), 'role' => 'admin', 'is_active' => true],
            ['name' => 'Manager User', 'email' => 'manager@bss.com', 'password' => Hash::make('manager123'), 'role' => 'manager', 'is_active' => true],
            ['name' => 'Treasurer User', 'email' => 'treasurer@bss.com', 'password' => Hash::make('treasurer123'), 'role' => 'treasurer', 'is_active' => true],
            ['name' => 'Member User', 'email' => 'member@bss.com', 'password' => Hash::make('member123'), 'role' => 'member', 'is_active' => true],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(['email' => $userData['email']], $userData);
        }

        // Members
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

        // Loans (matching actual schema: loan_id, member_id, amount, purpose, repayment_months, interest, monthly_payment, status)
        $loans = [
            ['loan_id' => 'LOAN001', 'member_id' => 'BSS002', 'amount' => 200000, 'purpose' => 'Business expansion', 'repayment_months' => 12, 'interest' => 10000, 'monthly_payment' => 17500, 'status' => 'approved'],
            ['loan_id' => 'LOAN002', 'member_id' => 'BSS003', 'amount' => 500000, 'purpose' => 'Home improvement', 'repayment_months' => 24, 'interest' => 25000, 'monthly_payment' => 21875, 'status' => 'approved'],
            ['loan_id' => 'LOAN003', 'member_id' => 'BSS005', 'amount' => 1000000, 'purpose' => 'Equipment purchase', 'repayment_months' => 24, 'interest' => 50000, 'monthly_payment' => 43750, 'status' => 'pending'],
        ];

        foreach ($loans as $loanData) {
            Loan::create($loanData);
        }

        // Transactions (matching actual schema: transaction_id, member_id, amount, type)
        $transactions = [
            ['transaction_id' => 'TXN001', 'member_id' => 'BSS001', 'amount' => 100000, 'type' => 'deposit'],
            ['transaction_id' => 'TXN002', 'member_id' => 'BSS002', 'amount' => 150000, 'type' => 'deposit'],
            ['transaction_id' => 'TXN003', 'member_id' => 'BSS003', 'amount' => 200000, 'type' => 'deposit'],
        ];

        foreach ($transactions as $transactionData) {
            Transaction::create($transactionData);
        }

        // Projects (matching actual schema: project_id, name, budget, timeline, description, progress, roi, risk_score)
        $projects = [
            ['project_id' => 'PRJ001', 'name' => 'Community Water Project', 'budget' => 5000000, 'timeline' => '2024-12-31', 'description' => 'Installing clean water systems', 'progress' => 65, 'roi' => 12.5, 'risk_score' => 30],
            ['project_id' => 'PRJ002', 'name' => 'Education Support Program', 'budget' => 3000000, 'timeline' => '2024-06-30', 'description' => 'Providing scholarships', 'progress' => 100, 'roi' => 8.0, 'risk_score' => 20],
            ['project_id' => 'PRJ003', 'name' => 'Healthcare Initiative', 'budget' => 8000000, 'timeline' => '2025-03-31', 'description' => 'Mobile health clinics', 'progress' => 15, 'roi' => 15.0, 'risk_score' => 40],
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }

        // Deposits (matching actual schema: first_sn, name, last_name, phone_number, account_number, deposit, amount, email)
        $deposits = [
            ['first_sn' => '001', 'name' => 'John', 'last_name' => 'Doe', 'phone_number' => '+256700123456', 'account_number' => 'BSS001', 'deposit' => 'savings', 'amount' => 100000, 'email' => 'john@bss.com'],
            ['first_sn' => '002', 'name' => 'Jane', 'last_name' => 'Smith', 'phone_number' => '+256700234567', 'account_number' => 'BSS002', 'deposit' => 'shares', 'amount' => 150000, 'email' => 'jane@bss.com'],
        ];

        foreach ($deposits as $depositData) {
            Deposit::create($depositData);
        }

        // Meetings (matching actual schema: title, description, scheduled_at, location, status, attendees, minutes, created_by)
        $meetings = [
            ['title' => 'Monthly General Meeting', 'description' => 'Regular monthly meeting', 'scheduled_at' => now()->addDays(7), 'location' => 'Community Center', 'status' => 'scheduled', 'attendees' => null, 'minutes' => null, 'created_by' => 'BSS005'],
            ['title' => 'Project Review Meeting', 'description' => 'Review project progress', 'scheduled_at' => now()->addDays(14), 'location' => 'BSS Office', 'status' => 'scheduled', 'attendees' => null, 'minutes' => null, 'created_by' => 'BSS004'],
        ];

        foreach ($meetings as $meetingData) {
            Meeting::create($meetingData);
        }

        // Documents
        $documents = [
            ['title' => 'Group Constitution', 'description' => 'Official constitution', 'file_path' => '/documents/constitution.pdf', 'file_type' => 'pdf', 'file_size' => 1024000],
            ['title' => 'Financial Report Q1', 'description' => 'Quarterly report', 'file_path' => '/documents/report_q1.pdf', 'file_type' => 'pdf', 'file_size' => 512000],
        ];

        foreach ($documents as $documentData) {
            Document::create($documentData);
        }

        // Notifications
        $notifications = [
            ['title' => 'Welcome to BSS System', 'message' => 'Welcome to the BSS Investment Group system!', 'type' => 'info', 'is_read' => false],
            ['title' => 'New Loan Application', 'message' => 'A new loan application has been submitted', 'type' => 'loan_application', 'is_read' => false],
        ];

        foreach ($notifications as $notificationData) {
            Notification::create($notificationData);
        }
    }
}