<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use App\Models\Project;
use App\Models\Meeting;
use App\Models\Document;
use App\Models\Share;
use App\Models\Dividend;
use App\Models\SavingsHistory;
use App\Models\Deposit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompleteDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedProjects();
        $this->seedMeetings();
        $this->seedDocuments();
        $this->seedShares();
        $this->seedDividends();
        $this->seedSavingsHistory();
        $this->seedDeposits();
        $this->seedBioData();
        $this->seedInvestmentOpportunities();
        $this->seedPortfolioPerformances();
        $this->seedFundraisings();
        $this->seedLoanApplications();
        $this->seedChatMessages();
        $this->seedReports();
        $this->seedSettings();
        $this->seedAuditLogs();
        $this->seedBackups();
        $this->seedGeneratedReports();
    }

    private function seedProjects()
    {
        // Only seed if no projects exist
        if (DB::table('projects')->count() > 0) {
            return;
        }
        
        $projects = [
            [
                'project_id' => 'PRJ001',
                'name' => 'Community Water Project',
                'description' => 'Installing clean water systems in rural communities',
                'budget' => 5000000,
                'timeline' => '2024-12-31',
                'progress' => 65,
                'roi' => 12.5,
                'risk_score' => 30,
                'potential_roi' => 15.0,
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'notes' => 'Project progressing well with community support',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => 'PRJ002',
                'name' => 'Education Support Program',
                'description' => 'Providing scholarships and educational materials',
                'budget' => 3000000,
                'timeline' => '2024-06-30',
                'progress' => 100,
                'roi' => 8.0,
                'risk_score' => 20,
                'potential_roi' => 10.0,
                'start_date' => '2024-03-01',
                'end_date' => '2024-06-30',
                'notes' => 'Successfully completed with excellent results',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => 'PRJ003',
                'name' => 'Healthcare Initiative',
                'description' => 'Mobile health clinics for remote areas',
                'budget' => 8000000,
                'timeline' => '2025-03-31',
                'progress' => 15,
                'roi' => 15.0,
                'risk_score' => 40,
                'potential_roi' => 18.0,
                'start_date' => '2024-06-01',
                'end_date' => '2025-03-31',
                'notes' => 'Initial planning phase completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($projects as $project) {
            DB::table('projects')->insert($project);
        }
    }

    private function seedMeetings()
    {
        $meetings = [
            [
                'title' => 'Monthly General Meeting',
                'description' => 'Regular monthly meeting to discuss group activities',
                'scheduled_at' => now()->addDays(7),
                'location' => 'Community Center, Kampala',
                'status' => 'scheduled',
                'attendees' => json_encode(['BSS0001', 'BSS0002', 'BSS0003']),
                'minutes' => null,
                'created_by' => 'BSS0001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Project Review Meeting',
                'description' => 'Review progress of ongoing projects',
                'scheduled_at' => now()->addDays(14),
                'location' => 'BSS Office',
                'status' => 'scheduled',
                'attendees' => json_encode(['BSS0001', 'BSS0004', 'BSS0005']),
                'minutes' => null,
                'created_by' => 'BSS0001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Financial Planning Session',
                'description' => 'Annual financial planning and budget review',
                'scheduled_at' => now()->subDays(30),
                'location' => 'Conference Room A',
                'status' => 'completed',
                'attendees' => json_encode(['BSS0001', 'BSS0002', 'BSS0003', 'BSS0004']),
                'minutes' => 'Discussed budget allocation for next year. Approved new investment strategies.',
                'created_by' => 'BSS0001',
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(30),
            ],
        ];

        foreach ($meetings as $meeting) {
            DB::table('meetings')->insert($meeting);
        }
    }

    private function seedDocuments()
    {
        $documents = [
            [
                'title' => 'Group Constitution',
                'filename' => 'constitution.pdf',
                'description' => 'Official constitution and bylaws',
                'file_path' => '/documents/constitution.pdf',
                'file_type' => 'pdf',
                'file_size' => 1024000,
                'category' => 'legal',
                'uploaded_by' => 'BSS0001',
                'access_roles' => json_encode(['admin', 'ceo', 'td']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Financial Report Q1 2024',
                'filename' => 'financial_report_q1.pdf',
                'description' => 'Quarterly financial report',
                'file_path' => '/documents/financial_report_q1.pdf',
                'file_type' => 'pdf',
                'file_size' => 512000,
                'category' => 'financial',
                'uploaded_by' => 'BSS0001',
                'access_roles' => json_encode(['admin', 'ceo', 'td', 'cashier']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Member Registration Form',
                'filename' => 'registration_form.docx',
                'description' => 'Template for new member registration',
                'file_path' => '/documents/registration_form.docx',
                'file_type' => 'docx',
                'file_size' => 256000,
                'category' => 'member',
                'uploaded_by' => 'BSS0001',
                'access_roles' => json_encode(['admin', 'cashier']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($documents as $document) {
            DB::table('documents')->insert($document);
        }
    }

    private function seedShares()
    {
        $shareholders = Member::where('role', 'shareholder')->get();
        
        foreach ($shareholders as $shareholder) {
            $sharesOwned = rand(10, 100);
            $shareValue = 10000;
            
            // Get the member's actual ID (not member_id string)
            DB::table('shares')->insert([
                'member_id' => $shareholder->id, // Use the actual ID, not member_id string
                'shares_owned' => $sharesOwned,
                'share_value' => $shareValue,
                'purchase_date' => now()->subMonths(rand(1, 12)),
                'certificate_number' => 'CERT' . str_pad($shareholder->id, 4, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedDividends()
    {
        $shareholders = Member::where('role', 'shareholder')->get();
        
        foreach ($shareholders as $shareholder) {
            for ($quarter = 1; $quarter <= 2; $quarter++) {
                DB::table('dividends')->insert([
                    'member_id' => $shareholder->member_id,
                    'amount' => rand(25000, 75000),
                    'year' => 2024,
                    'quarter' => $quarter,
                    'status' => 'paid',
                    'payment_date' => now()->subMonths(4 - $quarter),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedSavingsHistory()
    {
        $members = Member::all();
        
        foreach ($members as $member) {
            for ($i = 6; $i >= 0; $i--) {
                $amount = rand(50000, 200000);
                $month = now()->subMonths($i)->startOfMonth()->format('Y-m-d');
                
                DB::table('savings_history')->insert([
                    'member_id' => $member->member_id,
                    'amount' => $amount,
                    'month' => $month,
                    'created_at' => now()->subMonths($i),
                    'updated_at' => now()->subMonths($i),
                ]);
            }
        }
    }

    private function seedDeposits()
    {
        $members = Member::limit(5)->get();
        
        foreach ($members as $index => $member) {
            DB::table('deposits')->insert([
                'first_sn' => str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'name' => explode(' ', $member->full_name)[0],
                'last_name' => explode(' ', $member->full_name)[1] ?? '',
                'phone_number' => $member->contact,
                'account_number' => $member->member_id,
                'deposit' => ['savings', 'shares', 'loan_repayment'][rand(0, 2)],
                'amount' => rand(50000, 300000),
                'email' => $member->email,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedBioData()
    {
        $members = Member::limit(3)->get();
        
        foreach ($members as $member) {
            DB::table('bio_data')->insert([
                'full_name' => $member->full_name,
                'nin_no' => 'CM' . rand(1000000000000, 9999999999999),
                'present_address' => json_encode([
                    'street' => 'Main Street',
                    'city' => $member->location ?? 'Kampala',
                    'district' => $member->location ?? 'Kampala',
                    'country' => 'Uganda'
                ]),
                'permanent_address' => json_encode([
                    'street' => 'Home Street',
                    'city' => $member->location ?? 'Kampala',
                    'district' => $member->location ?? 'Kampala',
                    'country' => 'Uganda'
                ]),
                'telephone' => json_encode([
                    'mobile' => $member->contact ?? '+256700000000',
                    'home' => '+256700000000'
                ]),
                'email' => $member->email,
                'dob' => now()->subYears(rand(25, 60))->format('Y-m-d'),
                'nationality' => 'Ugandan',
                'birth_place' => json_encode([
                    'city' => 'Kampala',
                    'district' => 'Kampala',
                    'country' => 'Uganda'
                ]),
                'marital_status' => ['single', 'married', 'divorced', 'widowed'][rand(0, 3)],
                'spouse_name' => null,
                'spouse_nin' => null,
                'next_of_kin' => 'John Doe',
                'next_of_kin_nin' => 'CM' . rand(1000000000000, 9999999999999),
                'father_name' => 'Father Name',
                'mother_name' => 'Mother Name',
                'children' => json_encode([]),
                'occupation' => $member->occupation ?? 'Business',
                'signature' => 'signature_' . $member->id . '.png',
                'declaration_date' => now()->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedInvestmentOpportunities()
    {
        $opportunities = [
            [
                'title' => 'Real Estate Development',
                'description' => 'Investment in commercial real estate development project',
                'minimum_investment' => 1000000,
                'expected_return' => 15.5,
                'risk_level' => 'medium',
                'duration_months' => 24,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Agricultural Venture',
                'description' => 'Investment in modern farming techniques and equipment',
                'minimum_investment' => 500000,
                'expected_return' => 12.0,
                'risk_level' => 'low',
                'duration_months' => 12,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($opportunities as $opportunity) {
            DB::table('investment_opportunities')->insert($opportunity);
        }
    }

    private function seedPortfolioPerformances()
    {
        $members = Member::where('role', 'shareholder')->limit(3)->get();
        
        foreach ($members as $member) {
            for ($month = 6; $month >= 0; $month--) {
                DB::table('portfolio_performances')->insert([
                    'member_id' => $member->member_id,
                    'portfolio_value' => rand(500000, 2000000),
                    'returns' => rand(-50000, 150000),
                    'performance_date' => now()->subMonths($month)->startOfMonth(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedFundraisings()
    {
        $fundraisings = [
            [
                'title' => 'Community School Construction',
                'description' => 'Fundraising for building a new community school',
                'target_amount' => 10000000,
                'current_amount' => 6500000,
                'start_date' => now()->subMonths(3),
                'end_date' => now()->addMonths(3),
                'status' => 'active',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Medical Equipment Fund',
                'description' => 'Raising funds for medical equipment for local clinic',
                'target_amount' => 5000000,
                'current_amount' => 5000000,
                'start_date' => now()->subMonths(6),
                'end_date' => now()->subMonths(1),
                'status' => 'completed',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($fundraisings as $fundraising) {
            DB::table('fundraisings')->insert($fundraising);
        }

        // Add contributions
        $members = Member::limit(5)->get();
        foreach ($members as $member) {
            DB::table('fundraising_contributions')->insert([
                'fundraising_id' => 1,
                'member_id' => $member->member_id,
                'amount' => rand(100000, 500000),
                'contribution_date' => now()->subDays(rand(1, 90)),
                'payment_method' => ['cash', 'mobile_money', 'bank_transfer'][rand(0, 2)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Add expenses
        DB::table('fundraising_expenses')->insert([
            'fundraising_id' => 1,
            'description' => 'Construction materials',
            'amount' => 2000000,
            'expense_date' => now()->subDays(30),
            'approved_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedLoanApplications()
    {
        $members = Member::limit(3)->get();
        
        foreach ($members as $index => $member) {
            DB::table('loan_applications')->insert([
                'application_id' => 'APP' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'member_id' => $member->member_id,
                'amount_requested' => rand(500000, 2000000),
                'purpose' => ['Business expansion', 'Education', 'Home improvement'][rand(0, 2)],
                'repayment_period' => rand(6, 24),
                'monthly_income' => rand(300000, 1000000),
                'employment_status' => ['employed', 'self_employed', 'business_owner'][rand(0, 2)],
                'collateral_type' => ['property', 'vehicle', 'savings'][rand(0, 2)],
                'collateral_value' => rand(1000000, 5000000),
                'guarantor_name' => 'John Guarantor',
                'guarantor_contact' => '+256700123456',
                'status' => ['pending', 'approved', 'rejected'][rand(0, 2)],
                'application_date' => now()->subDays(rand(1, 30)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedChatMessages()
    {
        $users = User::limit(4)->get();
        
        foreach ($users as $user) {
            DB::table('chat_messages')->insert([
                'sender_id' => $user->id,
                'receiver_id' => User::where('id', '!=', $user->id)->first()->id,
                'message' => 'Hello, how are you doing today?',
                'is_read' => rand(0, 1),
                'created_at' => now()->subHours(rand(1, 48)),
                'updated_at' => now()->subHours(rand(1, 48)),
            ]);
        }
    }

    private function seedReports()
    {
        $reports = [
            [
                'title' => 'Monthly Financial Report',
                'type' => 'financial',
                'description' => 'Comprehensive monthly financial overview',
                'generated_by' => 1,
                'generated_at' => now()->subDays(7),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Member Activity Report',
                'type' => 'member',
                'description' => 'Report on member activities and engagement',
                'generated_by' => 1,
                'generated_at' => now()->subDays(14),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($reports as $report) {
            DB::table('reports')->insert($report);
        }
    }

    private function seedSettings()
    {
        DB::table('settings')->insert([
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedAuditLogs()
    {
        $users = User::limit(3)->get();
        
        foreach ($users as $user) {
            DB::table('audit_logs')->insert([
                'user_id' => $user->id,
                'action' => 'login',
                'description' => 'User logged into the system',
                'ip_address' => '192.168.1.' . rand(1, 255),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subHours(rand(1, 24)),
                'updated_at' => now()->subHours(rand(1, 24)),
            ]);
        }
    }

    private function seedBackups()
    {
        DB::table('backups')->insert([
            'filename' => 'backup_' . date('Y_m_d_H_i_s') . '.sql',
            'size' => rand(1000000, 10000000),
            'status' => 'completed',
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subDays(1),
        ]);
    }

    private function seedGeneratedReports()
    {
        DB::table('generated_reports')->insert([
            'title' => 'System Overview Report',
            'type' => 'system',
            'file_path' => '/reports/system_overview_' . date('Y_m_d') . '.pdf',
            'generated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}