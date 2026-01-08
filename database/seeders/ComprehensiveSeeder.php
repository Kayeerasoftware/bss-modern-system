<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use App\Models\Notification;
use App\Models\SavingsHistory;
use App\Models\Meeting;
use App\Models\Document;
use App\Models\Share;
use App\Models\Dividend;
use Illuminate\Support\Facades\Hash;

class ComprehensiveSeeder extends Seeder
{
    public function run(): void
    {
        // Create comprehensive sample members
        $members = [
            [
                'member_id' => 'BSS001',
                'full_name' => 'John Doe',
                'email' => 'john@example.com',
                'location' => 'Kampala',
                'occupation' => 'Teacher',
                'contact' => '+256700123456',
                'savings' => 500000,
                'loan' => 0,
                'balance' => 750000,
                'savings_balance' => 500000,
                'role' => 'client',
                'password' => Hash::make('password123')
            ],
            [
                'member_id' => 'BSS002',
                'full_name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'location' => 'Entebbe',
                'occupation' => 'Nurse',
                'contact' => '+256700234567',
                'savings' => 750000,
                'loan' => 200000,
                'balance' => 950000,
                'savings_balance' => 750000,
                'role' => 'shareholder',
                'password' => Hash::make('password123')
            ],
            [
                'member_id' => 'BSS003',
                'full_name' => 'Robert Johnson',
                'email' => 'robert@example.com',
                'location' => 'Jinja',
                'occupation' => 'Engineer',
                'contact' => '+256700345678',
                'savings' => 1200000,
                'loan' => 500000,
                'balance' => 1400000,
                'savings_balance' => 1200000,
                'role' => 'cashier',
                'password' => Hash::make('password123')
            ],
            [
                'member_id' => 'BSS004',
                'full_name' => 'Mary Wilson',
                'email' => 'mary@example.com',
                'location' => 'Mbarara',
                'occupation' => 'Doctor',
                'contact' => '+256700456789',
                'savings' => 2000000,
                'loan' => 0,
                'balance' => 2200000,
                'savings_balance' => 2000000,
                'role' => 'td',
                'password' => Hash::make('password123')
            ],
            [
                'member_id' => 'BSS005',
                'full_name' => 'David Brown',
                'email' => 'david@example.com',
                'location' => 'Gulu',
                'occupation' => 'Business Owner',
                'contact' => '+256700567890',
                'savings' => 3000000,
                'loan' => 1000000,
                'balance' => 3500000,
                'savings_balance' => 3000000,
                'role' => 'ceo',
                'password' => Hash::make('password123')
            ],
            [
                'member_id' => 'BSS006',
                'full_name' => 'Sarah Connor',
                'email' => 'sarah@example.com',
                'location' => 'Masaka',
                'occupation' => 'Accountant',
                'contact' => '+256700678901',
                'savings' => 800000,
                'loan' => 0,
                'balance' => 850000,
                'savings_balance' => 800000,
                'role' => 'client',
                'password' => Hash::make('password123')
            ]
        ];

        foreach ($members as $memberData) {
            Member::create($memberData);
        }

        // Create comprehensive projects
        $projects = [
            [
                'name' => 'Community Water Project',
                'description' => 'Installing clean water systems in rural communities',
                'budget' => 5000000,
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'status' => 'in_progress',
                'progress' => 65
            ],
            [
                'name' => 'Education Support Program',
                'description' => 'Providing scholarships and educational materials',
                'budget' => 3000000,
                'start_date' => '2024-03-01',
                'end_date' => '2024-06-30',
                'status' => 'completed',
                'progress' => 100
            ],
            [
                'name' => 'Healthcare Initiative',
                'description' => 'Mobile health clinics for remote areas',
                'budget' => 8000000,
                'start_date' => '2024-06-01',
                'end_date' => '2025-03-31',
                'status' => 'planning',
                'progress' => 15
            ],
            [
                'name' => 'Agricultural Development',
                'description' => 'Supporting local farmers with modern techniques',
                'budget' => 4500000,
                'start_date' => '2024-02-15',
                'end_date' => '2024-11-30',
                'status' => 'in_progress',
                'progress' => 40
            ]
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }

        // Create comprehensive loans
        $loans = [
            [
                'member_id' => 'BSS002',
                'amount' => 200000,
                'interest_rate' => 5,
                'duration_months' => 12,
                'purpose' => 'Small business expansion',
                'status' => 'active',
                'amount_paid' => 50000,
                'application_date' => now()->subMonths(2),
                'approved_date' => now()->subMonths(2)->addDays(3)
            ],
            [
                'member_id' => 'BSS003',
                'amount' => 500000,
                'interest_rate' => 5,
                'duration_months' => 24,
                'purpose' => 'Home improvement',
                'status' => 'active',
                'amount_paid' => 125000,
                'application_date' => now()->subMonths(3),
                'approved_date' => now()->subMonths(3)->addDays(2)
            ],
            [
                'member_id' => 'BSS005',
                'amount' => 1000000,
                'interest_rate' => 5,
                'duration_months' => 24,
                'purpose' => 'Equipment purchase',
                'status' => 'pending',
                'amount_paid' => 0,
                'application_date' => now()->subDays(5)
            ],
            [
                'member_id' => 'BSS001',
                'amount' => 300000,
                'interest_rate' => 5,
                'duration_months' => 6,
                'purpose' => 'Emergency medical expenses',
                'status' => 'completed',
                'amount_paid' => 315000,
                'application_date' => now()->subMonths(8),
                'approved_date' => now()->subMonths(8)->addDays(1)
            ]
        ];

        foreach ($loans as $loanData) {
            Loan::create($loanData);
        }

        // Create comprehensive transactions
        $transactions = [
            ['member_id' => 'BSS001', 'amount' => 100000, 'type' => 'deposit', 'description' => 'Monthly savings deposit', 'reference' => 'TXN001', 'status' => 'completed'],
            ['member_id' => 'BSS002', 'amount' => 50000, 'type' => 'withdrawal', 'description' => 'Emergency withdrawal', 'reference' => 'TXN002', 'status' => 'completed'],
            ['member_id' => 'BSS003', 'amount' => 200000, 'type' => 'deposit', 'description' => 'Quarterly savings', 'reference' => 'TXN003', 'status' => 'completed'],
            ['member_id' => 'BSS004', 'amount' => 300000, 'type' => 'deposit', 'description' => 'Bonus savings', 'reference' => 'TXN004', 'status' => 'completed'],
            ['member_id' => 'BSS005', 'amount' => 150000, 'type' => 'deposit', 'description' => 'Business profit sharing', 'reference' => 'TXN005', 'status' => 'completed'],
            ['member_id' => 'BSS006', 'amount' => 75000, 'type' => 'deposit', 'description' => 'Regular savings', 'reference' => 'TXN006', 'status' => 'completed']
        ];

        foreach ($transactions as $transactionData) {
            Transaction::create($transactionData);
        }

        // Create savings history
        $savingsHistory = [
            ['member_id' => 'BSS001', 'amount' => 100000, 'type' => 'deposit', 'description' => 'Monthly savings'],
            ['member_id' => 'BSS002', 'amount' => 150000, 'type' => 'deposit', 'description' => 'Quarterly savings'],
            ['member_id' => 'BSS003', 'amount' => 200000, 'type' => 'deposit', 'description' => 'Annual bonus savings'],
            ['member_id' => 'BSS004', 'amount' => 250000, 'type' => 'deposit', 'description' => 'Professional fee savings'],
            ['member_id' => 'BSS005', 'amount' => 300000, 'type' => 'deposit', 'description' => 'Business profit savings']
        ];

        foreach ($savingsHistory as $savingsData) {
            SavingsHistory::create($savingsData);
        }

        // Create shares
        $shares = [
            ['member_id' => 'BSS001', 'shares_owned' => 10, 'share_value' => 10000, 'purchase_date' => now()->subMonths(6), 'certificate_number' => 'CERT001'],
            ['member_id' => 'BSS002', 'shares_owned' => 15, 'share_value' => 10000, 'purchase_date' => now()->subMonths(8), 'certificate_number' => 'CERT002'],
            ['member_id' => 'BSS003', 'shares_owned' => 20, 'share_value' => 10000, 'purchase_date' => now()->subMonths(4), 'certificate_number' => 'CERT003'],
            ['member_id' => 'BSS004', 'shares_owned' => 25, 'share_value' => 10000, 'purchase_date' => now()->subMonths(10), 'certificate_number' => 'CERT004'],
            ['member_id' => 'BSS005', 'shares_owned' => 30, 'share_value' => 10000, 'purchase_date' => now()->subMonths(12), 'certificate_number' => 'CERT005']
        ];

        foreach ($shares as $shareData) {
            Share::create($shareData);
        }

        // Create meetings
        $meetings = [
            [
                'title' => 'Monthly General Meeting',
                'description' => 'Regular monthly meeting to discuss group activities',
                'date' => now()->addDays(7),
                'location' => 'Community Center, Kampala',
                'status' => 'scheduled'
            ],
            [
                'title' => 'Project Review Meeting',
                'description' => 'Review progress of ongoing projects',
                'date' => now()->addDays(14),
                'location' => 'BSS Office',
                'status' => 'scheduled'
            ],
            [
                'title' => 'Financial Planning Session',
                'description' => 'Annual financial planning and budget review',
                'date' => now()->subDays(30),
                'location' => 'Conference Room A',
                'status' => 'completed'
            ]
        ];

        foreach ($meetings as $meetingData) {
            Meeting::create($meetingData);
        }

        // Create documents
        $documents = [
            [
                'title' => 'Group Constitution',
                'description' => 'Official constitution and bylaws',
                'file_path' => '/documents/constitution.pdf',
                'file_type' => 'pdf',
                'uploaded_by' => 'BSS005'
            ],
            [
                'title' => 'Financial Report Q1 2024',
                'description' => 'Quarterly financial report',
                'file_path' => '/documents/financial_report_q1.pdf',
                'file_type' => 'pdf',
                'uploaded_by' => 'BSS004'
            ],
            [
                'title' => 'Member Registration Form',
                'description' => 'Template for new member registration',
                'file_path' => '/documents/registration_form.docx',
                'file_type' => 'docx',
                'uploaded_by' => 'BSS003'
            ]
        ];

        foreach ($documents as $documentData) {
            Document::create($documentData);
        }

        // Create notifications
        $notifications = [
            [
                'title' => 'New Loan Application',
                'message' => 'BSS005 has applied for a loan of UGX 1,000,000',
                'type' => 'loan_application',
                'is_read' => false
            ],
            [
                'title' => 'Monthly Meeting Reminder',
                'message' => 'Monthly group meeting scheduled for next Saturday',
                'type' => 'meeting',
                'is_read' => false
            ],
            [
                'title' => 'Project Update',
                'message' => 'Community Water Project is 65% complete',
                'type' => 'project_update',
                'is_read' => true
            ],
            [
                'title' => 'Loan Approved',
                'message' => 'Your loan application has been approved',
                'type' => 'loan_approval',
                'is_read' => false
            ],
            [
                'title' => 'New Member Joined',
                'message' => 'Sarah Connor has joined the group',
                'type' => 'member_update',
                'is_read' => true
            ]
        ];

        foreach ($notifications as $notificationData) {
            Notification::create($notificationData);
        }
    }
}