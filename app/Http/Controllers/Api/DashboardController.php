<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard data for CEO
     */
    public function getDashboardData()
    {
        try {
            // Get all members with their financial data
            $members = Member::with(['bioData', 'savingsHistory', 'loans'])
                ->select('members.*')
                ->leftJoin('bio_data', 'members.id', '=', 'bio_data.member_id')
                ->addSelect([
                    'savings' => Transaction::selectRaw('COALESCE(SUM(amount), 0)')
                        ->whereColumn('member_id', 'members.id')
                        ->where('transaction_type', 'savings'),
                    'loan' => Loan::selectRaw('COALESCE(SUM(amount), 0)')
                        ->whereColumn('member_id', 'members.id')
                        ->where('status', 'approved'),
                    'balance' => Transaction::selectRaw('COALESCE(SUM(amount), 0)')
                        ->whereColumn('member_id', 'members.id')
                ])
                ->get();

            // Get pending loans
            $pendingLoans = Loan::with('member')
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'members' => $members,
                'pending_loans' => $pendingLoans,
                'total_members' => $members->count(),
                'total_pending_loans' => $pendingLoans->count(),
                'total_pending_amount' => $pendingLoans->sum('amount')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading dashboard data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get CEO-specific data
     */
    public function getCeoData()
    {
        try {
            // Calculate executive metrics
            $totalMembers = Member::count();
            $totalRevenue = 45200000; // Mock data
            $netProfit = 12800000; // Mock data

            // Strategic initiatives
            $strategicInitiatives = [
                [
                    'id' => 1,
                    'title' => 'Digital Transformation',
                    'description' => 'Modernizing core systems',
                    'progress' => 75,
                    'budget' => 15000000,
                    'expectedROI' => 25,
                    'status' => 'on-track'
                ],
                [
                    'id' => 2,
                    'title' => 'Market Expansion',
                    'description' => 'Enter new regional markets',
                    'progress' => 45,
                    'budget' => 8000000,
                    'expectedROI' => 18,
                    'status' => 'on-track'
                ],
                [
                    'id' => 3,
                    'title' => 'Product Innovation',
                    'description' => 'Develop new financial products',
                    'progress' => 30,
                    'budget' => 12000000,
                    'expectedROI' => 22,
                    'status' => 'at-risk'
                ]
            ];

            // Key metrics
            $keyMetrics = [
                [
                    'name' => 'Customer Satisfaction',
                    'value' => 94,
                    'target' => 90,
                    'trend' => 'up'
                ],
                [
                    'name' => 'Employee Engagement',
                    'value' => 87,
                    'target' => 85,
                    'trend' => 'up'
                ],
                [
                    'name' => 'Operational Efficiency',
                    'value' => 92,
                    'target' => 88,
                    'trend' => 'up'
                ],
                [
                    'name' => 'Market Share',
                    'value' => 16,
                    'target' => 15,
                    'trend' => 'up'
                ]
            ];

            // Business segments
            $businessSegments = [
                'savings_deposits' => 35,
                'loans_credit' => 30,
                'investment_services' => 20,
                'insurance' => 10,
                'other_services' => 5
            ];

            // Revenue history (last 12 months)
            $revenueHistory = [
                3200000, 3400000, 3600000, 3800000, 3900000, 4100000,
                4200000, 4300000, 4400000, 4500000, 4600000, 4700000
            ];

            return response()->json([
                'success' => true,
                'executive_data' => [
                    'totalRevenue' => $totalRevenue,
                    'netProfit' => $netProfit,
                    'totalMembers' => $totalMembers
                ],
                'strategic_initiatives' => $strategicInitiatives,
                'key_metrics' => $keyMetrics,
                'business_segments' => $businessSegments,
                'revenue_history' => $revenueHistory
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading CEO data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate reports
     */
    public function generateReport(Request $request)
    {
        try {
            $type = $request->input('type', 'financial_summary');
            $format = $request->input('format', 'pdf');

            // Simulate report generation
            $reportTypes = [
                'financial_summary' => 'Financial Summary Report',
                'profit_loss' => 'Profit & Loss Statement',
                'balance_sheet' => 'Balance Sheet',
                'cash_flow' => 'Cash Flow Statement',
                'member_activity' => 'Member Activity Report',
                'loan_portfolio' => 'Loan Portfolio Analysis',
                'savings_analysis' => 'Savings Analysis Report',
                'compliance' => 'Compliance Report',
                'trend_analysis' => 'Trend Analysis Report',
                'forecast' => 'Forecast Report',
                'executive_summary' => 'Executive Summary Report'
            ];

            $reportName = $reportTypes[$type] ?? 'Custom Report';

            // Simulate processing time
            sleep(2);

            return response()->json([
                'success' => true,
                'message' => $reportName . ' generated successfully',
                'report_type' => $type,
                'format' => $format,
                'download_url' => route('download.report', ['type' => $type, 'format' => $format]),
                'estimated_time' => '2 minutes'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get report generation status
     */
    public function getReportStatus()
    {
        try {
            $reports = collect([
                [
                    'id' => 1,
                    'name' => 'Monthly Financial Report',
                    'status' => 'completed',
                    'progress' => 100,
                    'created_at' => now()->subHours(2),
                    'download_url' => '#'
                ],
                [
                    'id' => 2,
                    'name' => 'Member Activity Report',
                    'status' => 'processing',
                    'progress' => 65,
                    'created_at' => now()->subHours(1),
                    'download_url' => null
                ],
                [
                    'id' => 3,
                    'name' => 'Loan Portfolio Analysis',
                    'status' => 'pending',
                    'progress' => 0,
                    'created_at' => now()->addHours(1),
                    'download_url' => null
                ]
            ]);

            return response()->json([
                'success' => true,
                'reports' => $reports,
                'total_reports' => $reports->count(),
                'completed_reports' => $reports->filter(fn($r) => $r['status'] === 'completed')->count(),
                'processing_reports' => $reports->filter(fn($r) => $r['status'] === 'processing')->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting report status: ' . $e->getMessage()
            ], 500);
        }
    }
}
