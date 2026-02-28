<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use App\Models\Deposit;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function getDashboardAnalytics()
    {
        try {
            // Basic statistics
            $totalSavings = Member::sum('savings') ?: 0;
            $totalLoans = Loan::where('status', 'approved')->sum('amount') ?: 0;
            $totalMembers = Member::count() ?: 0;
            $activeProjects = Project::where('status', '!=', 'completed')->count() ?: 0;

            // Top savers
            $topSavers = Member::orderBy('savings', 'desc')
                ->take(5)
                ->get(['member_id', 'full_name', 'savings']);

            // Member distribution by role
            $membersByRole = Member::select('role', DB::raw('count(*) as count'))
                ->groupBy('role')
                ->get();

            // Loan status distribution
            $loansByStatus = Loan::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get();

            // Monthly transaction trends (last 6 months)
            $monthlyTransactions = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $deposits = Transaction::where('type', 'deposit')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount') ?: 0;
                
                $withdrawals = Transaction::where('type', 'withdrawal')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount') ?: 0;

                $monthlyTransactions[] = [
                    'month' => $month->format('M Y'),
                    'deposits' => $deposits,
                    'withdrawals' => $withdrawals
                ];
            }

            // Project progress data
            $projects = Project::select('name', 'progress', 'budget', 'status')->get();

            // Financial performance over time (quarterly)
            $quarterlyPerformance = [];
            for ($i = 5; $i >= 0; $i--) {
                $quarter = Carbon::now()->subQuarters($i);
                $quarterlyPerformance[] = [
                    'period' => 'Q' . $quarter->quarter . ' ' . $quarter->year,
                    'total_assets' => $this->calculateTotalAssets($quarter),
                    'member_savings' => $this->calculateMemberSavings($quarter),
                    'loan_portfolio' => $this->calculateLoanPortfolio($quarter)
                ];
            }

            // Recent activity
            $recentActivity = $this->getRecentActivity();

            // Loan repayment analysis
            $repaymentAnalysis = $this->getLoanRepaymentAnalysis();

            return response()->json([
                'totalSavings' => $totalSavings,
                'totalLoans' => $totalLoans,
                'totalMembers' => $totalMembers,
                'activeProjects' => $activeProjects,
                'topSavers' => $topSavers,
                'membersByRole' => $membersByRole,
                'loansByStatus' => $loansByStatus,
                'monthlyTransactions' => $monthlyTransactions,
                'projects' => $projects,
                'quarterlyPerformance' => $quarterlyPerformance,
                'recentActivity' => $recentActivity,
                'repaymentAnalysis' => $repaymentAnalysis
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load analytics data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function calculateTotalAssets($date)
    {
        // Calculate total assets up to the given date
        $savings = Member::where('created_at', '<=', $date)->sum('savings') ?: 0;
        $loans = Loan::where('created_at', '<=', $date)
            ->where('status', 'approved')
            ->sum('amount') ?: 0;
        
        return $savings + $loans;
    }

    private function calculateMemberSavings($date)
    {
        return Member::where('created_at', '<=', $date)->sum('savings') ?: 0;
    }

    private function calculateLoanPortfolio($date)
    {
        return Loan::where('created_at', '<=', $date)
            ->where('status', 'approved')
            ->sum('amount') ?: 0;
    }

    private function getRecentActivity()
    {
        $activities = [];

        // Recent transactions
        $recentTransactions = Transaction::orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        foreach ($recentTransactions as $transaction) {
            $activities[] = [
                'id' => 'txn_' . $transaction->id,
                'type' => $transaction->type,
                'description' => "Transaction: {$transaction->type} of " . number_format($transaction->amount) . " UGX",
                'time' => $transaction->created_at->diffForHumans()
            ];
        }

        // Recent loans
        $recentLoans = Loan::orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        foreach ($recentLoans as $loan) {
            $activities[] = [
                'id' => 'loan_' . $loan->id,
                'type' => 'loan',
                'description' => "Loan application: " . number_format($loan->amount) . " UGX for {$loan->purpose}",
                'time' => $loan->created_at->diffForHumans()
            ];
        }

        // Recent members
        $recentMembers = Member::orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        foreach ($recentMembers as $member) {
            $activities[] = [
                'id' => 'member_' . $member->id,
                'type' => 'member',
                'description' => "New member: {$member->full_name} joined",
                'time' => $member->created_at->diffForHumans()
            ];
        }

        // Sort by time and return top 5
        usort($activities, function($a, $b) {
            return strcmp($b['time'], $a['time']);
        });

        return array_slice($activities, 0, 5);
    }

    private function getLoanRepaymentAnalysis()
    {
        $totalLoans = Loan::where('status', 'approved')->count();
        
        if ($totalLoans == 0) {
            return [
                'on_time' => 0,
                'late' => 0,
                'defaulted' => 0
            ];
        }

        // Simplified analysis - in real implementation, you'd track payment dates
        $onTime = Loan::where('status', 'approved')
            ->where('paid_amount', '>', 0)
            ->count();
        
        $late = Loan::where('status', 'approved')
            ->where('paid_amount', 0)
            ->where('created_at', '<', Carbon::now()->subMonths(1))
            ->count();
        
        $defaulted = Loan::where('status', 'rejected')->count();

        return [
            'on_time' => $onTime,
            'late' => $late,
            'defaulted' => $defaulted
        ];
    }

    public function getFinancialSummary()
    {
        return response()->json([
            'total_savings' => Member::sum('savings') ?: 0,
            'total_loans' => Loan::sum('amount') ?: 0,
            'total_deposits' => Deposit::sum('amount') ?: 0,
            'total_transactions' => Transaction::sum('amount') ?: 0,
            'average_savings' => Member::avg('savings') ?: 0,
            'loan_default_rate' => $this->calculateDefaultRate(),
            'growth_rate' => $this->calculateGrowthRate()
        ]);
    }

    public function getMemberAnalytics()
    {
        return response()->json([
            'total_members' => Member::count(),
            'members_by_role' => Member::select('role', DB::raw('count(*) as count'))
                ->groupBy('role')
                ->get(),
            'members_by_location' => Member::select('location', DB::raw('count(*) as count'))
                ->groupBy('location')
                ->get(),
            'average_savings_by_role' => Member::select('role', DB::raw('avg(savings) as avg_savings'))
                ->groupBy('role')
                ->get()
        ]);
    }

    public function getProjectAnalytics()
    {
        return response()->json([
            'total_projects' => Project::count(),
            'projects_by_status' => Project::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
            'total_budget' => Project::sum('budget') ?: 0,
            'average_progress' => Project::avg('progress') ?: 0,
            'projects_completion_rate' => $this->calculateProjectCompletionRate()
        ]);
    }

    private function calculateDefaultRate()
    {
        $totalLoans = Loan::count();
        if ($totalLoans == 0) return 0;
        
        $defaultedLoans = Loan::where('status', 'rejected')->count();
        return ($defaultedLoans / $totalLoans) * 100;
    }

    private function calculateGrowthRate()
    {
        $currentMonth = Member::whereMonth('created_at', Carbon::now()->month)->sum('savings');
        $lastMonth = Member::whereMonth('created_at', Carbon::now()->subMonth()->month)->sum('savings');
        
        if ($lastMonth == 0) return 0;
        
        return (($currentMonth - $lastMonth) / $lastMonth) * 100;
    }

    private function calculateProjectCompletionRate()
    {
        $totalProjects = Project::count();
        if ($totalProjects == 0) return 0;
        
        $completedProjects = Project::where('status', 'completed')->count();
        return ($completedProjects / $totalProjects) * 100;
    }
}
