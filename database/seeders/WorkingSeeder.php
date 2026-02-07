<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use App\Models\BioData;
use App\Models\Notification;
use App\Models\SavingsHistory;
use App\Models\Meeting;
use App\Models\Document;
use App\Models\Share;
use App\Models\Dividend;
use App\Models\PortfolioPerformance;
use App\Models\InvestmentOpportunity;
use App\Models\ChatMessage;
use App\Models\Deposit;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class WorkingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users Table
        $users = [
            ['name' => 'System Admin', 'email' => 'admin@bss.com', 'password' => Hash::make('admin123'), 'role' => 'admin', 'is_active' => true],
            ['name' => 'TD User', 'email' => 'td@bss.com', 'password' => Hash::make('td123'), 'role' => 'td', 'is_active' => true],
            ['name' => 'Cashier User', 'email' => 'cashier@bss.com', 'password' => Hash::make('cashier123'), 'role' => 'cashier', 'is_active' => true],
            ['name' => 'CEO User', 'email' => 'ceo@bss.com', 'password' => Hash::make('ceo123'), 'role' => 'ceo', 'is_active' => true],
            ['name' => 'Client User', 'email' => 'client@bss.com', 'password' => Hash::make('client123'), 'role' => 'client', 'is_active' => true],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(['email' => $userData['email']], $userData);
        }

        // 2. Members Table (Comprehensive)
        $members = [
            ['member_id' => 'BSS001', 'full_name' => 'John Doe', 'email' => 'john@bss.com', 'location' => 'Kampala', 'occupation' => 'Teacher', 'contact' => '+256700123456', 'savings' => 2500000, 'loan' => 0, 'savings_balance' => 2500000, 'role' => 'shareholder', 'password' => Hash::make('password123'), 'profile_picture' => null],
            ['member_id' => 'BSS002', 'full_name' => 'Jane Smith', 'email' => 'jane@bss.com', 'location' => 'Entebbe', 'occupation' => 'Nurse', 'contact' => '+256700234567', 'savings' => 1800000, 'loan' => 500000, 'savings_balance' => 1800000, 'role' => 'shareholder', 'password' => Hash::make('password123'), 'profile_picture' => null],
            ['member_id' => 'BSS003', 'full_name' => 'Robert Johnson', 'email' => 'robert@bss.com', 'location' => 'Jinja', 'occupation' => 'Engineer', 'contact' => '+256700345678', 'savings' => 3200000, 'loan' => 800000, 'savings_balance' => 3200000, 'role' => 'cashier', 'password' => Hash::make('password123'), 'profile_picture' => null],
            ['member_id' => 'BSS004', 'full_name' => 'Mary Wilson', 'email' => 'mary@bss.com', 'location' => 'Mbarara', 'occupation' => 'Doctor', 'contact' => '+256700456789', 'savings' => 4500000, 'loan' => 0, 'savings_balance' => 4500000, 'role' => 'td', 'password' => Hash::make('password123'), 'profile_picture' => null],
            ['member_id' => 'BSS005', 'full_name' => 'David Brown', 'email' => 'david@bss.com', 'location' => 'Gulu', 'occupation' => 'Business Owner', 'contact' => '+256700567890', 'savings' => 6000000, 'loan' => 1500000, 'savings_balance' => 6000000, 'role' => 'ceo', 'password' => Hash::make('password123'), 'profile_picture' => null],
            ['member_id' => 'BSS006', 'full_name' => 'Sarah Davis', 'email' => 'sarah@bss.com', 'location' => 'Masaka', 'occupation' => 'Farmer', 'contact' => '+256700678901', 'savings' => 800000, 'loan' => 300000, 'savings_balance' => 800000, 'role' => 'client', 'password' => Hash::make('password123'), 'profile_picture' => null],
            ['member_id' => 'BSS007', 'full_name' => 'Michael Lee', 'email' => 'michael@bss.com', 'location' => 'Lira', 'occupation' => 'Teacher', 'contact' => '+256700789012', 'savings' => 1200000, 'loan' => 200000, 'savings_balance' => 1200000, 'role' => 'client', 'password' => Hash::make('password123'), 'profile_picture' => null],
            ['member_id' => 'BSS008', 'full_name' => 'Anna Garcia', 'email' => 'anna@bss.com', 'location' => 'Arua', 'occupation' => 'Nurse', 'contact' => '+256700890123', 'savings' => 950000, 'loan' => 150000, 'savings_balance' => 950000, 'role' => 'client', 'password' => Hash::make('password123'), 'profile_picture' => null],
        ];

        foreach ($members as $memberData) {
            Member::firstOrCreate(['member_id' => $memberData['member_id']], $memberData);
        }

        // 3. Loans Table
        $loans = [
            ['loan_id' => 'LOAN001', 'member_id' => 'BSS002', 'amount' => 500000, 'purpose' => 'Business expansion for medical practice', 'repayment_months' => 24, 'interest' => 25000, 'monthly_payment' => 22917, 'status' => 'approved'],
            ['loan_id' => 'LOAN002', 'member_id' => 'BSS003', 'amount' => 800000, 'purpose' => 'Home renovation and expansion', 'repayment_months' => 36, 'interest' => 40000, 'monthly_payment' => 24444, 'status' => 'approved'],
            ['loan_id' => 'LOAN003', 'member_id' => 'BSS005', 'amount' => 1500000, 'purpose' => 'Equipment purchase for business', 'repayment_months' => 48, 'interest' => 75000, 'monthly_payment' => 34375, 'status' => 'approved'],
            ['loan_id' => 'LOAN004', 'member_id' => 'BSS006', 'amount' => 300000, 'purpose' => 'Agricultural inputs and equipment', 'repayment_months' => 12, 'interest' => 15000, 'monthly_payment' => 27500, 'status' => 'approved'],
            ['loan_id' => 'LOAN005', 'member_id' => 'BSS007', 'amount' => 200000, 'purpose' => 'Educational materials and supplies', 'repayment_months' => 18, 'interest' => 10000, 'monthly_payment' => 12222, 'status' => 'pending'],
            ['loan_id' => 'LOAN006', 'member_id' => 'BSS008', 'amount' => 150000, 'purpose' => 'Medical equipment purchase', 'repayment_months' => 12, 'interest' => 7500, 'monthly_payment' => 13750, 'status' => 'approved'],
        ];

        foreach ($loans as $loanData) {
            Loan::create($loanData);
        }

        // 4. Transactions Table
        $transactions = [
            ['transaction_id' => 'TXN001', 'member_id' => 'BSS001', 'amount' => 500000, 'type' => 'deposit', 'description' => 'Monthly savings'],
            ['transaction_id' => 'TXN002', 'member_id' => 'BSS001', 'amount' => 300000, 'type' => 'deposit', 'description' => 'Salary deposit'],
            ['transaction_id' => 'BSS002', 'member_id' => 'BSS002', 'amount' => 400000, 'type' => 'deposit', 'description' => 'Business income'],
            ['transaction_id' => 'TXN004', 'member_id' => 'BSS003', 'amount' => 600000, 'type' => 'deposit', 'description' => 'Investment return'],
            ['transaction_id' => 'TXN005', 'member_id' => 'BSS004', 'amount' => 800000, 'type' => 'deposit', 'description' => 'Monthly savings'],
            ['transaction_id' => 'TXN006', 'member_id' => 'BSS005', 'amount' => 1000000, 'type' => 'deposit', 'description' => 'Salary deposit'],
            ['transaction_id' => 'TXN007', 'member_id' => 'BSS006', 'amount' => 200000, 'type' => 'deposit', 'description' => 'Business income'],
            ['transaction_id' => 'TXN008', 'member_id' => 'BSS007', 'amount' => 250000, 'type' => 'deposit', 'description' => 'Monthly savings'],
            ['transaction_id' => 'TXN009', 'member_id' => 'BSS008', 'amount' => 180000, 'type' => 'deposit', 'description' => 'Salary deposit'],
            ['transaction_id' => 'TXN010', 'member_id' => 'BSS001', 'amount' => 100000, 'type' => 'withdrawal', 'description' => 'Emergency withdrawal'],
            ['transaction_id' => 'TXN011', 'member_id' => 'BSS002', 'amount' => 150000, 'type' => 'withdrawal', 'description' => 'School fees'],
            ['transaction_id' => 'TXN012', 'member_id' => 'BSS003', 'amount' => 200000, 'type' => 'withdrawal', 'description' => 'Medical expenses'],
            ['transaction_id' => 'TXN013', 'member_id' => 'BSS001', 'amount' => 50000, 'type' => 'condolence', 'description' => 'Funeral support'],
            ['transaction_id' => 'TXN014', 'member_id' => 'BSS002', 'amount' => 75000, 'type' => 'condolence', 'description' => 'Bereavement fund'],
            ['transaction_id' => 'TXN015', 'member_id' => 'BSS003', 'amount' => 100000, 'type' => 'condolence', 'description' => 'Condolence contribution'],
        ];

        foreach ($transactions as $transactionData) {
            Transaction::create($transactionData);
        }

        // 5. Projects Table
        $projects = [
            ['project_id' => 'PRJ001', 'name' => 'Community Water Project', 'budget' => 15000000, 'timeline' => '2024-12-31', 'description' => 'Installing clean water systems in 5 rural communities', 'progress' => 75, 'roi' => 12.5, 'expected_roi' => 15.0, 'actual_roi' => 12.5, 'risk_score' => 25],
            ['project_id' => 'PRJ002', 'name' => 'Education Support Program', 'budget' => 8000000, 'timeline' => '2024-08-31', 'description' => 'Providing scholarships and educational materials to 200 students', 'progress' => 90, 'roi' => 8.5, 'expected_roi' => 10.0, 'actual_roi' => 8.5, 'risk_score' => 15],
            ['project_id' => 'PRJ003', 'name' => 'Healthcare Initiative', 'budget' => 25000000, 'timeline' => '2025-02-28', 'description' => 'Mobile health clinics and medical equipment deployment', 'progress' => 45, 'roi' => 15.2, 'expected_roi' => 18.0, 'actual_roi' => 15.2, 'risk_score' => 30],
            ['project_id' => 'PRJ004', 'name' => 'Agricultural Expansion', 'budget' => 12000000, 'timeline' => '2024-10-31', 'description' => 'Modern farming equipment and training programs', 'progress' => 60, 'roi' => 14.8, 'expected_roi' => 16.5, 'actual_roi' => 14.8, 'risk_score' => 20],
            ['project_id' => 'PRJ005', 'name' => 'Digital Banking Platform', 'budget' => 30000000, 'timeline' => '2025-06-30', 'description' => 'Mobile and web banking solution development', 'progress' => 25, 'roi' => 18.5, 'expected_roi' => 22.0, 'actual_roi' => 18.5, 'risk_score' => 35],
            ['project_id' => 'PRJ006', 'name' => 'Microfinance Expansion', 'budget' => 18000000, 'timeline' => '2024-11-30', 'description' => 'Expanding microfinance services to new regions', 'progress' => 40, 'roi' => 16.2, 'expected_roi' => 19.0, 'actual_roi' => 16.2, 'risk_score' => 28],
        ];

        foreach ($projects as $projectData) {
            Project::firstOrCreate(['project_id' => $projectData['project_id']], $projectData);
        }

        // 6. Bio Data Table
        $bioData = [
            [
                'full_name' => 'John Doe',
                'nin_no' => 'CM1234567890123X',
                'present_address' => ['street' => '123 Main St', 'city' => 'Kampala', 'country' => 'Uganda'],
                'permanent_address' => ['street' => '123 Main St', 'city' => 'Kampala', 'country' => 'Uganda'],
                'telephone' => ['mobile' => '+256700123456', 'home' => '+256414123456'],
                'email' => 'john@bss.com',
                'dob' => '1985-03-15',
                'nationality' => 'Ugandan',
                'birth_place' => ['district' => 'Kampala', 'country' => 'Uganda'],
                'marital_status' => 'married',
                'spouse_name' => 'Jane Doe',
                'spouse_nin' => 'CF1234567890123X',
                'next_of_kin' => 'Jane Doe',
                'next_of_kin_nin' => 'CF1234567890123X',
                'father_name' => 'Robert Doe Sr.',
                'mother_name' => 'Mary Doe',
                'children' => [['name' => 'Alice Doe', 'dob' => '2010-05-10'], ['name' => 'Bob Doe', 'dob' => '2012-08-15'], ['name' => 'Charlie Doe', 'dob' => '2015-03-20']],
                'occupation' => 'Teacher',
                'signature' => 'John Doe',
                'declaration_date' => '2024-01-15'
            ],
            [
                'full_name' => 'Jane Smith',
                'nin_no' => 'CF2345678901234X',
                'present_address' => ['street' => '456 Oak Ave', 'city' => 'Entebbe', 'country' => 'Uganda'],
                'permanent_address' => ['street' => '456 Oak Ave', 'city' => 'Entebbe', 'country' => 'Uganda'],
                'telephone' => ['mobile' => '+256700234567'],
                'email' => 'jane@bss.com',
                'dob' => '1988-07-22',
                'nationality' => 'Ugandan',
                'birth_place' => ['district' => 'Entebbe', 'country' => 'Uganda'],
                'marital_status' => 'single',
                'next_of_kin' => 'Robert Smith',
                'father_name' => 'James Smith',
                'mother_name' => 'Sarah Smith',
                'children' => [['name' => 'Emma Smith', 'dob' => '2018-12-05']],
                'occupation' => 'Nurse',
                'signature' => 'Jane Smith',
                'declaration_date' => '2024-01-20'
            ],
        ];

        foreach ($bioData as $bio) {
            BioData::create($bio);
        }

        // 7. Notifications Table
        $notifications = [
            ['title' => 'Welcome to BSS Investment Group', 'message' => 'Welcome! Your membership is now active. Start exploring investment opportunities.', 'roles' => json_encode(['client', 'shareholder', 'cashier', 'td', 'ceo']), 'type' => 'info', 'is_read' => false, 'created_by' => 'system'],
            ['title' => 'New Loan Application Submitted', 'message' => 'A new loan application has been submitted and is pending approval.', 'roles' => json_encode(['cashier', 'manager', 'admin']), 'type' => 'info', 'is_read' => false, 'created_by' => 'BSS003'],
            ['title' => 'Dividend Payment Processed', 'message' => 'Q1 2024 dividend payments have been processed and deposited to member accounts.', 'roles' => json_encode(['shareholder', 'ceo', 'admin']), 'type' => 'success', 'is_read' => false, 'created_by' => 'BSS004'],
            ['title' => 'Monthly General Meeting', 'message' => 'Monthly general meeting scheduled for next Friday at 2:00 PM.', 'roles' => json_encode(['client', 'shareholder', 'cashier', 'td', 'ceo']), 'type' => 'info', 'is_read' => false, 'created_by' => 'BSS005'],
            ['title' => 'System Maintenance Notice', 'message' => 'Scheduled system maintenance will occur this Sunday from 2:00 AM to 4:00 AM.', 'roles' => json_encode(['admin', 'manager']), 'type' => 'warning', 'is_read' => false, 'created_by' => 'system'],
            ['title' => 'New Investment Opportunity', 'message' => 'Agricultural Expansion project is now accepting investments with 14.8% expected ROI.', 'roles' => json_encode(['shareholder', 'ceo']), 'type' => 'info', 'is_read' => false, 'created_by' => 'BSS004'],
        ];

        foreach ($notifications as $notification) {
            Notification::create($notification);
        }

        // 8. Savings History Table
        $savingsHistory = [];
        $memberIds = ['BSS001', 'BSS002', 'BSS003', 'BSS004', 'BSS005', 'BSS006', 'BSS007', 'BSS008'];

        foreach ($memberIds as $memberId) {
            for ($i = 12; $i >= 1; $i--) {
                $month = Carbon::now()->subMonths($i)->format('Y-m-d');
                $amount = rand(100000, 500000); // Monthly savings amount

                $savingsHistory[] = [
                    'member_id' => $memberId,
                    'amount' => $amount,
                    'month' => $month
                ];
            }
        }

        foreach ($savingsHistory as $history) {
            SavingsHistory::create($history);
        }

        // 9. Meetings Table
        $meetings = [
            ['title' => 'Monthly General Meeting', 'description' => 'Regular monthly meeting to discuss group activities and financial performance', 'scheduled_at' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'), 'location' => 'BSS Community Center, Kampala', 'status' => 'scheduled', 'attendees' => json_encode(['BSS001', 'BSS002', 'BSS003', 'BSS004', 'BSS005']), 'minutes' => null, 'created_by' => 'BSS005'],
            ['title' => 'Project Review Meeting', 'description' => 'Review progress on all active investment projects', 'scheduled_at' => Carbon::now()->addDays(14)->format('Y-m-d H:i:s'), 'location' => 'BSS Office Conference Room', 'status' => 'scheduled', 'attendees' => json_encode(['BSS004', 'BSS005', 'BSS003']), 'minutes' => null, 'created_by' => 'BSS004'],
            ['title' => 'Annual General Meeting', 'description' => 'Annual general meeting for shareholders and members', 'scheduled_at' => Carbon::now()->addMonths(2)->format('Y-m-d H:i:s'), 'location' => 'Hotel Africana, Kampala', 'status' => 'scheduled', 'attendees' => json_encode(['BSS001', 'BSS002', 'BSS004', 'BSS005']), 'minutes' => null, 'created_by' => 'BSS005'],
            ['title' => 'Loan Committee Meeting', 'description' => 'Review and approve pending loan applications', 'scheduled_at' => Carbon::now()->addDays(3)->format('Y-m-d H:i:s'), 'location' => 'BSS Office Board Room', 'status' => 'scheduled', 'attendees' => json_encode(['BSS003', 'BSS004', 'BSS005']), 'minutes' => null, 'created_by' => 'BSS003'],
        ];

        foreach ($meetings as $meeting) {
            Meeting::create($meeting);
        }

        // 10. Documents Table
        $documents = [
            ['title' => 'BSS Constitution & By-laws', 'filename' => 'constitution_2024.pdf', 'file_path' => '/documents/constitution_2024.pdf', 'file_type' => 'pdf', 'file_size' => 2048000, 'category' => 'legal', 'description' => 'Official constitution and by-laws of BSS Investment Group', 'uploaded_by' => 'BSS005', 'access_roles' => json_encode(['admin', 'manager', 'shareholder'])],
            ['title' => 'Financial Report Q1 2024', 'filename' => 'financial_report_q1_2024.pdf', 'file_path' => '/documents/financial_report_q1_2024.pdf', 'file_type' => 'pdf', 'file_size' => 1536000, 'category' => 'financial', 'description' => 'Quarterly financial report and performance summary', 'uploaded_by' => 'BSS004', 'access_roles' => json_encode(['admin', 'treasurer', 'ceo'])],
            ['title' => 'Investment Policy Guidelines', 'filename' => 'investment_policy.pdf', 'file_path' => '/documents/investment_policy.pdf', 'file_type' => 'pdf', 'file_size' => 1024000, 'category' => 'other', 'description' => 'Guidelines for investment decisions and risk management', 'uploaded_by' => 'BSS005', 'access_roles' => json_encode(['admin', 'manager', 'shareholder'])],
            ['title' => 'Loan Application Form', 'filename' => 'loan_application_form.pdf', 'file_path' => '/documents/loan_application_form.pdf', 'file_type' => 'pdf', 'file_size' => 512000, 'category' => 'other', 'description' => 'Standard loan application form for members', 'uploaded_by' => 'BSS003', 'access_roles' => json_encode(['client', 'shareholder', 'cashier'])],
            ['title' => 'Annual Audit Report 2023', 'filename' => 'audit_report_2023.pdf', 'file_path' => '/documents/audit_report_2023.pdf', 'file_type' => 'pdf', 'file_size' => 3072000, 'category' => 'financial', 'description' => 'Independent audit report for financial year 2023', 'uploaded_by' => 'BSS004', 'access_roles' => json_encode(['admin', 'treasurer', 'ceo'])],
            ['title' => 'Member Handbook', 'filename' => 'member_handbook.pdf', 'file_path' => '/documents/member_handbook.pdf', 'file_type' => 'pdf', 'file_size' => 2560000, 'category' => 'other', 'description' => 'Comprehensive guide for BSS members', 'uploaded_by' => 'BSS005', 'access_roles' => json_encode(['client', 'shareholder', 'cashier', 'td', 'ceo'])],
        ];

        foreach ($documents as $document) {
            Document::create($document);
        }

        // 11. Shares Table
        $shareholders = [
            ['member_id_str' => 'BSS001', 'shares_owned' => 1500, 'share_value' => 1800, 'purchase_date' => '2023-01-15', 'certificate_number' => 'SHR001'],
            ['member_id_str' => 'BSS002', 'shares_owned' => 1200, 'share_value' => 1800, 'purchase_date' => '2023-02-10', 'certificate_number' => 'SHR002'],
            ['member_id_str' => 'BSS004', 'shares_owned' => 2500, 'share_value' => 1800, 'purchase_date' => '2023-03-20', 'certificate_number' => 'SHR003'],
            ['member_id_str' => 'BSS005', 'shares_owned' => 3333, 'share_value' => 1800, 'purchase_date' => '2023-01-05', 'certificate_number' => 'SHR004'],
        ];

        foreach ($shareholders as $share) {
            $member = Member::where('member_id', $share['member_id_str'])->first();
            if ($member) {
                Share::firstOrCreate(
                    ['member_id' => $member->id],
                    [
                        'member_id' => $member->id,
                        'shares_owned' => $share['shares_owned'],
                        'share_value' => $share['share_value'],
                        'purchase_date' => $share['purchase_date'],
                        'certificate_number' => $share['certificate_number'],
                    ]
                );
            }
        }

        // 12. Dividends Table
        $dividends = [
            // Q1 2024
            ['member_id' => 'BSS001', 'amount' => 135000, 'year' => 2024, 'quarter' => 1, 'payment_date' => '2024-03-31', 'status' => 'paid'],
            ['member_id' => 'BSS002', 'amount' => 108000, 'year' => 2024, 'quarter' => 1, 'payment_date' => '2024-03-31', 'status' => 'paid'],
            ['member_id' => 'BSS004', 'amount' => 225000, 'year' => 2024, 'quarter' => 1, 'payment_date' => '2024-03-31', 'status' => 'paid'],
            ['member_id' => 'BSS005', 'amount' => 300000, 'year' => 2024, 'quarter' => 1, 'payment_date' => '2024-03-31', 'status' => 'paid'],

            // Q4 2023
            ['member_id' => 'BSS001', 'amount' => 128000, 'year' => 2023, 'quarter' => 4, 'payment_date' => '2023-12-31', 'status' => 'paid'],
            ['member_id' => 'BSS002', 'amount' => 102000, 'year' => 2023, 'quarter' => 4, 'payment_date' => '2023-12-31', 'status' => 'paid'],
            ['member_id' => 'BSS004', 'amount' => 213000, 'year' => 2023, 'quarter' => 4, 'payment_date' => '2023-12-31', 'status' => 'paid'],
            ['member_id' => 'BSS005', 'amount' => 284000, 'year' => 2023, 'quarter' => 4, 'payment_date' => '2023-12-31', 'status' => 'paid'],

            // Q3 2023
            ['member_id' => 'BSS001', 'amount' => 122000, 'year' => 2023, 'quarter' => 3, 'payment_date' => '2023-09-30', 'status' => 'paid'],
            ['member_id' => 'BSS002', 'amount' => 98000, 'year' => 2023, 'quarter' => 3, 'payment_date' => '2023-09-30', 'status' => 'paid'],
            ['member_id' => 'BSS004', 'amount' => 204000, 'year' => 2023, 'quarter' => 3, 'payment_date' => '2023-09-30', 'status' => 'paid'],
            ['member_id' => 'BSS005', 'amount' => 272000, 'year' => 2023, 'quarter' => 3, 'payment_date' => '2023-09-30', 'status' => 'paid'],
        ];

        foreach ($dividends as $dividend) {
            Dividend::firstOrCreate(
                ['member_id' => $dividend['member_id'], 'year' => $dividend['year'], 'quarter' => $dividend['quarter']],
                $dividend
            );
        }

        // 13. Portfolio Performances Table
        $portfolioPerformances = [];
        $shareholderIds = ['BSS001', 'BSS002', 'BSS004', 'BSS005'];

        foreach ($shareholderIds as $memberId) {
            for ($i = 12; $i >= 1; $i--) {
                $date = Carbon::now()->subMonths($i)->startOfMonth();
                $portfolioPerformances[] = [
                    'member_id' => $memberId,
                    'period' => $date,
                    'portfolio_value' => rand(2000000, 6000000),
                    'market_value' => rand(1900000, 5800000),
                    'performance_percentage' => rand(80, 120) / 10,
                    'benchmark_comparison' => rand(-30, 50) / 10,
                ];
            }
        }

        foreach ($portfolioPerformances as $performance) {
            PortfolioPerformance::create($performance);
        }

        // 14. Investment Opportunities Table
        $investmentOpportunities = [
            [
                'title' => 'Renewable Energy Project',
                'description' => 'Solar power installation for rural communities with sustainable energy solutions and long-term ROI',
                'target_amount' => 50000000,
                'minimum_investment' => 500000,
                'expected_roi' => 18.5,
                'risk_level' => 'medium',
                'launch_date' => Carbon::now()->subDays(10),
                'deadline' => Carbon::now()->addDays(50),
                'status' => 'active'
            ],
            [
                'title' => 'Tech Innovation Hub',
                'description' => 'Co-working space and startup incubator for technology entrepreneurs in Kampala',
                'target_amount' => 75000000,
                'minimum_investment' => 1000000,
                'expected_roi' => 22.0,
                'risk_level' => 'high',
                'launch_date' => Carbon::now()->addDays(15),
                'deadline' => Carbon::now()->addDays(75),
                'status' => 'upcoming'
            ],
            [
                'title' => 'Agricultural Expansion',
                'description' => 'Modern farming equipment and training program for local farmers in Eastern Uganda',
                'target_amount' => 30000000,
                'minimum_investment' => 300000,
                'expected_roi' => 14.5,
                'risk_level' => 'low',
                'launch_date' => Carbon::now()->subDays(5),
                'deadline' => Carbon::now()->addDays(25),
                'status' => 'active'
            ],
            [
                'title' => 'Healthcare Technology',
                'description' => 'Digital health platform connecting rural clinics with urban hospitals',
                'target_amount' => 40000000,
                'minimum_investment' => 750000,
                'expected_roi' => 16.8,
                'risk_level' => 'medium',
                'launch_date' => Carbon::now()->addDays(30),
                'deadline' => Carbon::now()->addDays(90),
                'status' => 'upcoming'
            ],
        ];

        foreach ($investmentOpportunities as $opportunity) {
            InvestmentOpportunity::create($opportunity);
        }

        // 15. Chat Messages Table
        $chatMessages = [
            ['sender_id' => 'BSS001', 'receiver_id' => 'BSS002', 'message' => 'Hello Jane, how are the loan repayments going?', 'is_read' => true],
            ['sender_id' => 'BSS002', 'receiver_id' => 'BSS001', 'message' => 'Hi John! Going well, made my payment this month. How about you?', 'is_read' => true],
            ['sender_id' => 'BSS001', 'receiver_id' => 'BSS002', 'message' => 'Great to hear! I\'m doing well, dividends came in nicely this quarter.', 'is_read' => true],
            ['sender_id' => 'BSS003', 'receiver_id' => 'BSS005', 'message' => 'CEO, we have 3 new loan applications pending approval.', 'is_read' => false],
            ['sender_id' => 'BSS005', 'receiver_id' => 'BSS003', 'message' => 'Thanks Robert, I\'ll review them today. Any urgent ones?', 'is_read' => false],
            ['sender_id' => 'BSS004', 'receiver_id' => 'BSS001', 'message' => 'John, the water project is progressing well. 75% complete now.', 'is_read' => true],
            ['sender_id' => 'BSS001', 'receiver_id' => 'BSS004', 'message' => 'Excellent progress! Looking forward to the completion.', 'is_read' => true],
        ];

        foreach ($chatMessages as $message) {
            ChatMessage::create($message);
        }

        // 16. Deposits Table
        $deposits = [
            ['first_sn' => '001', 'name' => 'John', 'last_name' => 'Doe', 'phone_number' => '+256700123456', 'account_number' => 'BSS001', 'deposit' => 'savings', 'amount' => 500000, 'email' => 'john@bss.com'],
            ['first_sn' => '002', 'name' => 'Jane', 'last_name' => 'Smith', 'phone_number' => '+256700234567', 'account_number' => 'BSS002', 'deposit' => 'shares', 'amount' => 400000, 'email' => 'jane@bss.com'],
            ['first_sn' => '003', 'name' => 'Robert', 'last_name' => 'Johnson', 'phone_number' => '+256700345678', 'account_number' => 'BSS003', 'deposit' => 'savings', 'amount' => 600000, 'email' => 'robert@bss.com'],
            ['first_sn' => '004', 'name' => 'Mary', 'last_name' => 'Wilson', 'phone_number' => '+256700456789', 'account_number' => 'BSS004', 'deposit' => 'shares', 'amount' => 800000, 'email' => 'mary@bss.com'],
            ['first_sn' => '005', 'name' => 'David', 'last_name' => 'Brown', 'phone_number' => '+256700567890', 'account_number' => 'BSS005', 'deposit' => 'savings', 'amount' => 1000000, 'email' => 'david@bss.com'],
            ['first_sn' => '006', 'name' => 'Sarah', 'last_name' => 'Davis', 'phone_number' => '+256700678901', 'account_number' => 'BSS006', 'deposit' => 'savings', 'amount' => 200000, 'email' => 'sarah@bss.com'],
        ];

        foreach ($deposits as $deposit) {
            Deposit::create($deposit);
        }

        $this->command->info('All 18 database tables have been seeded with comprehensive data!');
    }
}
