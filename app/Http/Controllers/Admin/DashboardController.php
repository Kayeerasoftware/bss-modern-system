<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fundraising;
use App\Models\Loan;
use App\Models\Loans\LoanApplication;
use App\Models\Member;
use App\Models\Project;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('admin_dashboard:stats:v1', now()->addSeconds(60), function () {
            $memberSummary = Member::query()
                ->selectRaw('COUNT(*) as total_members, COALESCE(SUM(savings_balance), 0) as total_savings, COALESCE(SUM(balance), 0) as total_balance')
                ->first();

            $userSummary = User::query()
                ->selectRaw('COUNT(*) as total_users, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_users')
                ->first();

            $loanSummary = Loan::query()
                ->selectRaw('COUNT(*) as total_loans, SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_loans, SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_loans, COALESCE(SUM(amount), 0) as total_loan_amount')
                ->first();

            $transactionSummary = Transaction::query()
                ->selectRaw('COUNT(*) as total_transactions, SUM(CASE WHEN DATE(created_at) = CURRENT_DATE THEN 1 ELSE 0 END) as today_transactions')
                ->first();

            $projectSummary = Project::query()
                ->selectRaw('COUNT(*) as total_projects, SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_projects')
                ->first();

            $fundraisingSummary = Fundraising::query()
                ->selectRaw('SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_fundraisings, COALESCE(SUM(CASE WHEN status = "active" THEN target_amount ELSE 0 END), 0) as total_fundraising_target, COALESCE(SUM(CASE WHEN status = "active" THEN raised_amount ELSE 0 END), 0) as total_fundraising_raised')
                ->first();

            return [
                'totalMembers' => (int) ($memberSummary->total_members ?? 0),
                'newMembersThisMonth' => Member::query()->where('created_at', '>=', now()->startOfMonth())->count(),
                'totalUsers' => (int) ($userSummary->total_users ?? 0),
                'activeUsers' => (int) ($userSummary->active_users ?? 0),
                'totalLoans' => (int) ($loanSummary->total_loans ?? 0),
                'pendingLoans' => (int) ($loanSummary->pending_loans ?? 0),
                'approvedLoans' => (int) ($loanSummary->approved_loans ?? 0),
                'totalLoanAmount' => (float) ($loanSummary->total_loan_amount ?? 0),
                'pendingApplications' => LoanApplication::query()->where('status', 'pending')->count(),
                'totalTransactions' => (int) ($transactionSummary->total_transactions ?? 0),
                'todayTransactions' => (int) ($transactionSummary->today_transactions ?? 0),
                'totalProjects' => (int) ($projectSummary->total_projects ?? 0),
                'activeProjects' => (int) ($projectSummary->active_projects ?? 0),
                'activeFundraisings' => (int) ($fundraisingSummary->active_fundraisings ?? 0),
                'totalFundraisingTarget' => (float) ($fundraisingSummary->total_fundraising_target ?? 0),
                'totalFundraisingRaised' => (float) ($fundraisingSummary->total_fundraising_raised ?? 0),
                'totalAssets' => (float) (($memberSummary->total_savings ?? 0) + ($memberSummary->total_balance ?? 0)),
                'totalSavings' => (float) ($memberSummary->total_savings ?? 0),
            ];
        });

        $recentMembers = Cache::remember('admin_dashboard:recent_members:v1', now()->addSeconds(30), static function () {
            return Member::query()
                ->select('id', 'user_id', 'member_id', 'full_name', 'profile_picture', 'created_at')
                ->latest()
                ->take(5)
                ->get();
        });

        $recentLoans = Cache::remember('admin_dashboard:recent_loans:v1', now()->addSeconds(30), static function () {
            return Loan::query()->with('member')->latest()->take(5)->get();
        });

        $recentTransactions = Cache::remember('admin_dashboard:recent_transactions:v1', now()->addSeconds(30), static function () {
            return Transaction::query()->latest()->take(5)->get();
        });

        $monthlyData = $this->getMonthlyData((string) now()->year);

        return view('admin.dashboard', compact('stats', 'recentMembers', 'recentLoans', 'recentTransactions', 'monthlyData'));
    }

    private function getMonthlyData($year = null)
    {
        $yearKey = $year === null ? 'all' : (string) $year;

        return Cache::remember('admin_dashboard:monthly_data:'.$yearKey, now()->addSeconds(120), function () use ($year) {
            [$months, $members, $loans, $transactions, $revenue, $expenses, $loanAmounts] = $this->buildMonthlySeries($year);

            $savingsGrowth = array_fill(0, count($months), 0);
            $memberRetention = array_fill(0, count($months), 50);
            $loanRepaymentRate = array_fill(0, count($months), 75);

            // Predictive analytics
            $membersPrediction = $this->predictNextMonth($members);
            $loansPrediction = $this->predictNextMonth($loans);
            $revenuePrediction = $this->predictNextMonth($revenue);

            return [
                'months' => $months,
                'members' => $members,
                'loans' => $loans,
                'transactions' => $transactions,
                'revenue' => $revenue,
                'expenses' => $expenses,
                'loanAmounts' => $loanAmounts,
                'savingsGrowth' => $savingsGrowth,
                'memberRetention' => $memberRetention,
                'loanRepaymentRate' => $loanRepaymentRate,
                'predictions' => [
                    'members' => $membersPrediction,
                    'loans' => $loansPrediction,
                    'revenue' => $revenuePrediction,
                ],
                'analytics' => [
                    'avgMemberGrowth' => round(array_sum($members) / max(count($members), 1), 1),
                    'avgLoanGrowth' => round(array_sum($loans) / max(count($loans), 1), 1),
                    'avgRevenue' => round(array_sum($revenue) / max(count($revenue), 1), 2),
                    'profitMargin' => round((array_sum($revenue) - array_sum($expenses)) / max(array_sum($revenue), 1) * 100, 1),
                    'memberChurnRate' => round(100 - (array_sum($memberRetention) / max(count($memberRetention), 1)), 1),
                    'avgRepaymentRate' => round(array_sum($loanRepaymentRate) / max(count($loanRepaymentRate), 1), 1),
                ],
            ];
        });
    }

    private function buildMonthlySeries($year): array
    {
        $months = [];
        $members = [];
        $loans = [];
        $transactions = [];
        $revenue = [];
        $expenses = [];
        $loanAmounts = [];

        if ($year === 'all' || $year === null) {
            $currentYear = now()->year;

            $memberCounts = $this->toMap(
                Member::query()
                    ->selectRaw('YEAR(created_at) as period, COUNT(*) as total')
                    ->groupBy('period')
                    ->get(),
                'period'
            );

            $loanRows = $this->toMap(
                Loan::query()
                    ->selectRaw('YEAR(created_at) as period, COUNT(*) as total, COALESCE(SUM(amount), 0) as total_amount')
                    ->groupBy('period')
                    ->get(),
                'period'
            );

            $transactionRows = $this->toMap(
                Transaction::query()
                    ->selectRaw('YEAR(created_at) as period, COUNT(*) as total, COALESCE(SUM(CASE WHEN type = "deposit" THEN amount ELSE 0 END), 0) as total_revenue, COALESCE(SUM(CASE WHEN type = "withdrawal" THEN amount ELSE 0 END), 0) as total_expenses')
                    ->groupBy('period')
                    ->get(),
                'period'
            );

            for ($y = 2023; $y <= $currentYear; $y++) {
                $key = (string) $y;
                $months[] = $key;
                $members[] = (int) (($memberCounts[$key]['total'] ?? 0));
                $loans[] = (int) (($loanRows[$key]['total'] ?? 0));
                $transactions[] = (int) (($transactionRows[$key]['total'] ?? 0));
                $revenue[] = round(((float) ($transactionRows[$key]['total_revenue'] ?? 0)) / 1000000, 2);
                $expenses[] = round(((float) ($transactionRows[$key]['total_expenses'] ?? 0)) / 1000000, 2);
                $loanAmounts[] = round(((float) ($loanRows[$key]['total_amount'] ?? 0)) / 1000000, 2);
            }

            return [$months, $members, $loans, $transactions, $revenue, $expenses, $loanAmounts];
        }

        $selectedYear = (int) $year;

        $memberCounts = $this->toMap(
            Member::query()
                ->whereYear('created_at', $selectedYear)
                ->selectRaw('MONTH(created_at) as period, COUNT(*) as total')
                ->groupBy('period')
                ->get(),
            'period'
        );

        $loanRows = $this->toMap(
            Loan::query()
                ->whereYear('created_at', $selectedYear)
                ->selectRaw('MONTH(created_at) as period, COUNT(*) as total, COALESCE(SUM(amount), 0) as total_amount')
                ->groupBy('period')
                ->get(),
            'period'
        );

        $transactionRows = $this->toMap(
            Transaction::query()
                ->whereYear('created_at', $selectedYear)
                ->selectRaw('MONTH(created_at) as period, COUNT(*) as total, COALESCE(SUM(CASE WHEN type = "deposit" THEN amount ELSE 0 END), 0) as total_revenue, COALESCE(SUM(CASE WHEN type = "withdrawal" THEN amount ELSE 0 END), 0) as total_expenses')
                ->groupBy('period')
                ->get(),
            'period'
        );

        for ($month = 1; $month <= 12; $month++) {
            $key = (string) $month;
            $months[] = Carbon::create($selectedYear, $month, 1)->format('M Y');
            $members[] = (int) (($memberCounts[$key]['total'] ?? 0));
            $loans[] = (int) (($loanRows[$key]['total'] ?? 0));
            $transactions[] = (int) (($transactionRows[$key]['total'] ?? 0));
            $revenue[] = round(((float) ($transactionRows[$key]['total_revenue'] ?? 0)) / 1000000, 2);
            $expenses[] = round(((float) ($transactionRows[$key]['total_expenses'] ?? 0)) / 1000000, 2);
            $loanAmounts[] = round(((float) ($loanRows[$key]['total_amount'] ?? 0)) / 1000000, 2);
        }

        return [$months, $members, $loans, $transactions, $revenue, $expenses, $loanAmounts];
    }

    private function predictNextMonth($data)
    {
        $n = count($data);
        if ($n < 3) {
            return round((float) (end($data) ?: 0), 2);
        }

        // Linear regression for prediction
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        foreach ($data as $x => $y) {
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }

        $denominator = ($n * $sumX2 - $sumX * $sumX);
        if ($denominator == 0.0) {
            return round((float) (end($data) ?: 0), 2);
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / $denominator;
        $intercept = ($sumY - $slope * $sumX) / $n;

        return round($slope * $n + $intercept, 2);
    }

    public function getData()
    {
        try {
            $year = request('year');
            $yearKey = $year === null ? 'all' : (string) $year;

            $data = Cache::remember('admin_dashboard:data_response:'.$yearKey, now()->addSeconds(60), function () use ($year) {
                $monthly = $this->getMonthlyData($year);
                $selectedYear = ($year && $year !== 'all') ? (int) $year : null;

                $memberScope = Member::query();
                $loanScope = Loan::query();
                $projectScope = Project::query();
                $fundraisingScope = Fundraising::query();

                if ($selectedYear !== null) {
                    $memberScope->whereYear('created_at', $selectedYear);
                    $loanScope->whereYear('created_at', $selectedYear);
                    $projectScope->whereYear('created_at', $selectedYear);
                    $fundraisingScope->whereYear('created_at', $selectedYear);
                }

                $memberSummary = (clone $memberScope)
                    ->selectRaw('COUNT(*) as total_members, COALESCE(SUM(savings_balance), 0) as total_savings, COALESCE(SUM(balance), 0) as total_balance')
                    ->first();

                $loanSummary = (clone $loanScope)
                    ->selectRaw('COUNT(*) as total_loans, SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_loans, SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_loans, COALESCE(SUM(amount), 0) as total_loan_amount')
                    ->first();

                $monthly['stats'] = [
                    'totalMembers' => (int) ($memberSummary->total_members ?? 0),
                    'totalLoans' => (int) ($loanSummary->total_loans ?? 0),
                    'totalProjects' => (clone $projectScope)->count(),
                    'activeFundraisings' => (clone $fundraisingScope)->where('status', 'active')->count(),
                    'approvedLoans' => (int) ($loanSummary->approved_loans ?? 0),
                    'pendingLoans' => (int) ($loanSummary->pending_loans ?? 0),
                    'totalSavings' => (float) ($memberSummary->total_savings ?? 0),
                    'totalLoanAmount' => (float) ($loanSummary->total_loan_amount ?? 0),
                    'totalAssets' => (float) (($memberSummary->total_savings ?? 0) + ($memberSummary->total_balance ?? 0)),
                ];

                return $monthly;
            });

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function toMap(Collection $rows, string $keyField): array
    {
        $result = [];

        foreach ($rows as $row) {
            $key = (string) $row->{$keyField};
            $result[$key] = (array) $row;
        }

        return $result;
    }
}
