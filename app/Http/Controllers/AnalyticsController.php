<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function getDashboardAnalytics()
    {
        try {
            // Basic statistics
            $totalSavings = Member::transactionSavingsTotal() ?: 0;
            $totalLoans = Loan::status('approved')->sum('amount') ?: 0;
            $totalMembers = Member::count() ?: 0;
            $activeProjects = Project::where('status', '!=', 'completed')->count() ?: 0;

            // Top savers
            $topSavers = Member::query()
                ->withTransactionSavings()
                ->orderByRaw('COALESCE(member_transaction_savings.transaction_savings, 0) desc')
                ->take(5)
                ->get();

            // Member distribution by role
            $membersByRole = DB::table('member_roles')
                ->join('roles', 'roles.id', '=', 'member_roles.role_id')
                ->selectRaw('LOWER(roles.name) as role, COUNT(DISTINCT member_roles.member_id) as count')
                ->groupBy('roles.name')
                ->get();

            // Loan status distribution
            $loansByStatus = Loan::select('status_id', DB::raw('count(*) as count'))
                ->groupBy('status_id')
                ->get();

            // Monthly transaction trends (last 6 months)
            $monthlyTransactions = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $deposits = Transaction::query()->ofType('deposit')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount') ?: 0;
                
                $withdrawals = Transaction::query()->ofType('withdrawal')
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
            $projects = Project::query()
                ->select('id', 'name', 'progress_percentage', 'budget_amount', 'status_id')
                ->get()
                ->map(fn (Project $project) => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'progress' => $project->progress,
                    'budget' => $project->budget,
                    'status' => $project->status,
                ]);

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
        $savings = $this->savingsBalanceAtDate($date);
        $loans = Loan::where('created_at', '<=', $date)
            ->status('approved')
            ->sum('amount') ?: 0;
        
        return $savings + $loans;
    }

    private function calculateMemberSavings($date)
    {
        return $this->savingsBalanceAtDate($date);
    }

    private function calculateLoanPortfolio($date)
    {
        return Loan::where('created_at', '<=', $date)
            ->status('approved')
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
        $totalLoans = Loan::status('approved')->count();
        
        if ($totalLoans == 0) {
            return [
                'on_time' => 0,
                'late' => 0,
                'defaulted' => 0
            ];
        }

        if (!Loan::hasPaymentTrackingColumn()) {
            return [
                'on_time' => 0,
                'late' => 0,
            'defaulted' => Loan::status('rejected')->count(),
        ];
        }

        $paidColumn = Loan::paymentTrackingColumn();

        // Simplified analysis - in real implementation, you'd track payment dates
        $onTime = Loan::status('approved')
            ->where($paidColumn, '>', 0)
            ->count();
        
        $late = Loan::status('approved')
            ->where($paidColumn, 0)
            ->where('created_at', '<', Carbon::now()->subMonths(1))
            ->count();
        
        $defaulted = Loan::status('rejected')->count();

        return [
            'on_time' => $onTime,
            'late' => $late,
            'defaulted' => $defaulted
        ];
    }

    public function getFinancialSummary()
    {
        return response()->json([
            'total_savings' => Member::transactionSavingsTotal() ?: 0,
            'total_loans' => Loan::sum('amount') ?: 0,
            'total_deposits' => Transaction::query()->ofType('deposit')->sum('amount') ?: 0,
            'total_transactions' => Transaction::sum('amount') ?: 0,
            'average_savings' => (float) (Member::query()
                ->withTransactionSavings()
                ->selectRaw('AVG(COALESCE(member_transaction_savings.transaction_savings, 0)) as avg_savings')
                ->value('avg_savings') ?? 0),
            'loan_default_rate' => $this->calculateDefaultRate(),
            'growth_rate' => $this->calculateGrowthRate()
        ]);
    }

    public function getMemberAnalytics()
    {
        return response()->json([
            'total_members' => Member::count(),
            'members_by_role' => DB::table('member_roles')
                ->join('roles', 'roles.id', '=', 'member_roles.role_id')
                ->selectRaw('LOWER(roles.name) as role, COUNT(DISTINCT member_roles.member_id) as count')
                ->groupBy('roles.name')
                ->get(),
            'members_by_location' => Member::query()
                ->leftJoin('nationalities', 'nationalities.id', '=', 'members.nationality_id')
                ->selectRaw('COALESCE(nationalities.name, "Unknown") as location, COUNT(*) as count')
                ->groupBy('nationalities.name')
                ->get(),
            'average_savings_by_role' => Member::query()
                ->withTransactionSavings()
                ->leftJoin('member_roles', 'member_roles.member_id', '=', 'members.id')
                ->leftJoin('roles', 'roles.id', '=', 'member_roles.role_id')
                ->selectRaw('LOWER(COALESCE(roles.name, "unassigned")) as role, AVG(COALESCE(member_transaction_savings.transaction_savings, 0)) as avg_savings')
                ->groupBy('roles.name')
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
        
        $defaultedLoans = Loan::status('rejected')->count();
        return ($defaultedLoans / $totalLoans) * 100;
    }

    private function calculateGrowthRate()
    {
        $currentMonth = $this->savingsBalanceAtDate(Carbon::now()->endOfMonth());
        $lastMonth = $this->savingsBalanceAtDate(Carbon::now()->subMonthNoOverflow()->endOfMonth());
        
        if ($lastMonth == 0) return 0;
        
        return (($currentMonth - $lastMonth) / $lastMonth) * 100;
    }

    private function savingsBalanceAtDate(Carbon|string|null $date = null): float
    {
        $query = Transaction::query()
            ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
            ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
            ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
            ->where('ts.name', 'completed')
            ->whereNull('transactions.deleted_at')
            ->where('tc.affects_savings', 1);

        if ($date !== null) {
            $query->whereDate('transactions.created_at', '<=', $date);
        }

        $amountSql = 'COALESCE(NULLIF(transactions.net_amount, 0), transactions.amount, 0)';

        return (float) ($query
            ->selectRaw("COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN {$amountSql} ELSE -{$amountSql} END), 0) as balance")
            ->value('balance') ?? 0);
    }

    private function calculateProjectCompletionRate()
    {
        $totalProjects = Project::count();
        if ($totalProjects == 0) return 0;
        
        $completedProjects = Project::where('status', 'completed')->count();
        return ($completedProjects / $totalProjects) * 100;
    }
}
