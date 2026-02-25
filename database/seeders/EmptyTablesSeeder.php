<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class EmptyTablesSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBioData();
        $this->seedChatMessages();
        $this->seedFundraisings();
        $this->seedInvestmentOpportunities();
        $this->seedPortfolioPerformances();
        $this->seedLoanApplications();
        $this->seedReports();
        $this->seedSettings();
        $this->seedAuditLogs();
        $this->seedBackups();
        $this->seedGeneratedReports();
        $this->seedNotifications();
    }

    private function seedBioData()
    {
        if (DB::table('bio_data')->count() > 0) return;
        
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

    private function seedChatMessages()
    {
        if (DB::table('chat_messages')->count() > 0) return;
        
        $members = Member::limit(4)->get();
        
        foreach ($members as $member) {
            $receiver = Member::where('member_id', '!=', $member->member_id)->first();
            if ($receiver) {
                DB::table('chat_messages')->insert([
                    'sender_id' => $member->member_id,
                    'receiver_id' => $receiver->member_id,
                    'message' => 'Hello, how are you doing today?',
                    'is_read' => rand(0, 1),
                    'created_at' => now()->subHours(rand(1, 48)),
                    'updated_at' => now()->subHours(rand(1, 48)),
                ]);
            }
        }
    }

    private function seedFundraisings()
    {
        if (DB::table('fundraisings')->count() > 0) return;
        
        $fundraisings = [
            [
                'campaign_id' => 'CAMP001',
                'title' => 'Community School Construction',
                'description' => 'Fundraising for building a new community school',
                'target_amount' => 10000000,
                'raised_amount' => 6500000,
                'start_date' => now()->subMonths(3)->format('Y-m-d'),
                'end_date' => now()->addMonths(3)->format('Y-m-d'),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'campaign_id' => 'CAMP002',
                'title' => 'Medical Equipment Fund',
                'description' => 'Raising funds for medical equipment for local clinic',
                'target_amount' => 5000000,
                'raised_amount' => 5000000,
                'start_date' => now()->subMonths(6)->format('Y-m-d'),
                'end_date' => now()->subMonths(1)->format('Y-m-d'),
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($fundraisings as $fundraising) {
            DB::table('fundraisings')->insert($fundraising);
        }

        // Add contributions
        $members = Member::limit(5)->get();
        foreach ($members as $index => $member) {
            DB::table('fundraising_contributions')->insert([
                'contribution_id' => 'CONT' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'fundraising_id' => 1,
                'contributor_name' => $member->full_name,
                'contributor_email' => $member->email,
                'contributor_phone' => $member->contact,
                'amount' => rand(100000, 500000),
                'payment_method' => ['cash', 'mobile_money', 'bank_transfer'][rand(0, 2)],
                'notes' => 'Contribution from member',
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

    private function seedInvestmentOpportunities()
    {
        if (DB::table('investment_opportunities')->count() > 0) return;
        
        $opportunities = [
            [
                'title' => 'Real Estate Development',
                'description' => 'Investment in commercial real estate development project',
                'target_amount' => 10000000,
                'minimum_investment' => 1000000,
                'expected_roi' => 15.5,
                'risk_level' => 'medium',
                'launch_date' => now()->format('Y-m-d'),
                'deadline' => now()->addMonths(24)->format('Y-m-d'),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Agricultural Venture',
                'description' => 'Investment in modern farming techniques and equipment',
                'target_amount' => 5000000,
                'minimum_investment' => 500000,
                'expected_roi' => 12.0,
                'risk_level' => 'low',
                'launch_date' => now()->format('Y-m-d'),
                'deadline' => now()->addMonths(12)->format('Y-m-d'),
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
        if (DB::table('portfolio_performances')->count() > 0) return;
        
        $members = Member::where('role', 'shareholder')->limit(3)->get();
        
        foreach ($members as $member) {
            for ($month = 6; $month >= 0; $month--) {
                $portfolioValue = rand(500000, 2000000);
                $marketValue = $portfolioValue + rand(-100000, 200000);
                
                DB::table('portfolio_performances')->insert([
                    'member_id' => $member->member_id,
                    'period' => now()->subMonths($month)->startOfMonth()->format('Y-m-d'),
                    'portfolio_value' => $portfolioValue,
                    'market_value' => $marketValue,
                    'performance_percentage' => rand(-10, 25),
                    'benchmark_comparison' => rand(-5, 15),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedLoanApplications()
    {
        if (DB::table('loan_applications')->count() > 0) return;
        
        $members = Member::limit(3)->get();
        
        foreach ($members as $index => $member) {
            DB::table('loan_applications')->insert([
                'application_id' => 'APP' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'member_id' => $member->member_id,
                'amount' => rand(500000, 2000000),
                'purpose' => ['Business expansion', 'Education', 'Home improvement'][rand(0, 2)],
                'repayment_months' => rand(6, 24),
                'interest_rate' => 10.00,
                'status' => ['pending', 'approved', 'rejected'][rand(0, 2)],
                'rejection_reason' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedReports()
    {
        if (DB::table('reports')->count() > 0) return;
        
        $reports = [
            [
                'type' => 'financial',
                'format' => 'pdf',
                'date' => now()->subDays(7)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'member',
                'format' => 'excel',
                'date' => now()->subDays(14)->format('Y-m-d'),
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
        if (DB::table('settings')->count() > 0) return;
        
        $settings = [
            ['key' => 'app_name', 'value' => 'BSS Investment Group', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'app_version', 'value' => '1.0.0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_mode', 'value' => 'false', 'created_at' => now(), 'updated_at' => now()],
        ];
        
        foreach ($settings as $setting) {
            DB::table('settings')->insert($setting);
        }
    }

    private function seedAuditLogs()
    {
        if (DB::table('audit_logs')->count() > 0) return;
        
        $users = User::limit(3)->get();
        
        foreach ($users as $user) {
            DB::table('audit_logs')->insert([
                'user' => $user->name,
                'action' => 'login',
                'details' => 'User logged into the system',
                'timestamp' => now()->subHours(rand(1, 24)),
                'created_at' => now()->subHours(rand(1, 24)),
                'updated_at' => now()->subHours(rand(1, 24)),
            ]);
        }
    }

    private function seedBackups()
    {
        if (DB::table('backups')->count() > 0) return;
        
        DB::table('backups')->insert([
            'filename' => 'backup_' . date('Y_m_d_H_i_s') . '.sql',
            'path' => '/backups/backup_' . date('Y_m_d_H_i_s') . '.sql',
            'size' => rand(1000000, 10000000),
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subDays(1),
        ]);
    }

    private function seedGeneratedReports()
    {
        if (DB::table('generated_reports')->count() > 0) return;
        
        DB::table('generated_reports')->insert([
            'name' => 'System Overview Report',
            'type' => 'system',
            'from_date' => now()->subMonth()->format('Y-m-d'),
            'to_date' => now()->format('Y-m-d'),
            'format' => 'pdf',
            'file_path' => '/reports/system_overview_' . date('Y_m_d') . '.pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedNotifications()
    {
        if (DB::table('notifications')->count() > 0) return;
        
        $notifications = [
            [
                'title' => 'Welcome to BSS Investment Group',
                'message' => 'Thank you for joining BSS Investment Group. We are excited to have you as part of our community.',
                'roles' => json_encode(['client', 'shareholder', 'cashier', 'td', 'ceo']),
                'type' => 'success',
                'is_read' => false,
                'created_by' => 'admin',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'title' => 'Monthly Meeting Reminder',
                'message' => 'Our monthly general meeting is scheduled for next week. Please mark your calendars and ensure attendance.',
                'roles' => json_encode(['shareholder', 'ceo', 'td']),
                'type' => 'warning',
                'is_read' => false,
                'created_by' => 'admin',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'title' => 'Loan Payment Due',
                'message' => 'This is a reminder that your loan payment is due in 5 days. Please make arrangements to avoid late fees.',
                'roles' => json_encode(['client']),
                'type' => 'error',
                'is_read' => false,
                'created_by' => 'admin',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
        ];

        foreach ($notifications as $notification) {
            DB::table('notifications')->insert($notification);
        }
    }
}