<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fundraising;
use App\Models\FundraisingStatus;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanStatus;
use App\Models\Member;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\User;
use App\Services\DashboardStatsService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('admin_dashboard:stats:v2', now()->addSeconds(60), function () {
            $viewStats = app(DashboardStatsService::class)->get();
            $memberSummary = Member::query()
                ->selectRaw('COUNT(*) as total_members')
                ->first();

            $totalSavings = (float) DB::table('savings_accounts')->sum('current_balance');

            $userSummary = User::query()
                ->whereHas('member')
                ->selectRaw('COUNT(*) as total_users, SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_users')
                ->first();

            $pendingStatusId = LoanStatus::query()->where('name', 'pending')->value('id');
            $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');

            $loanSummary = Loan::query()
                ->selectRaw('COUNT(*) as total_loans, COALESCE(SUM(principal_amount), 0) as total_loan_amount')
                ->first();
            $pendingLoansCount = $viewStats['pending_loans_count'] ?? ($pendingStatusId ? Loan::query()->where('status_id', $pendingStatusId)->count() : 0);
            $approvedLoansCount = $approvedStatusId ? Loan::query()->where('status_id', $approvedStatusId)->count() : 0;

            $transactionSummary = Transaction::query()
                ->selectRaw('COUNT(*) as total_transactions, SUM(CASE WHEN DATE(created_at) = CURRENT_DATE THEN 1 ELSE 0 END) as today_transactions')
                ->first();

            $activeProjectStatusId = ProjectStatus::query()->where('name', 'active')->value('id');
            $projectSummary = Project::query()
                ->selectRaw('COUNT(*) as total_projects, SUM(CASE WHEN status_id = ? THEN 1 ELSE 0 END) as active_projects', [$activeProjectStatusId])
                ->first();

            $activeFundraisingStatusId = FundraisingStatus::query()->where('name', 'active')->value('id');
            $fundraisingSummary = Fundraising::query()
                ->selectRaw('SUM(CASE WHEN status_id = ? THEN 1 ELSE 0 END) as active_fundraisings, COALESCE(SUM(CASE WHEN status_id = ? THEN target_amount ELSE 0 END), 0) as total_fundraising_target, COALESCE(SUM(CASE WHEN status_id = ? THEN raised_amount ELSE 0 END), 0) as total_fundraising_raised', [
                    $activeFundraisingStatusId,
                    $activeFundraisingStatusId,
                    $activeFundraisingStatusId,
                ])
                ->first();

            return [
                'totalMembers' => (int) ($viewStats['total_members'] ?? $memberSummary->total_members ?? 0),
                'newMembersThisMonth' => Member::query()->where('created_at', '>=', now()->startOfMonth())->count(),
                'totalUsers' => (int) ($userSummary->total_users ?? 0),
                'activeUsers' => (int) ($userSummary->active_users ?? 0),
                'totalLoans' => (int) ($loanSummary->total_loans ?? 0),
                'pendingLoans' => (int) $pendingLoansCount,
                'approvedLoans' => (int) $approvedLoansCount,
                'totalLoanAmount' => (float) ($loanSummary->total_loan_amount ?? 0),
                'pendingApplications' => $pendingStatusId ? LoanApplication::query()->where('status_id', $pendingStatusId)->count() : 0,
                'totalTransactions' => (int) ($transactionSummary->total_transactions ?? 0),
                'todayTransactions' => (int) ($transactionSummary->today_transactions ?? 0),
                'totalProjects' => (int) ($projectSummary->total_projects ?? 0),
                'activeProjects' => (int) ($viewStats['active_projects'] ?? $projectSummary->active_projects ?? 0),
                'activeFundraisings' => (int) ($fundraisingSummary->active_fundraisings ?? 0),
                'totalFundraisingTarget' => (float) ($fundraisingSummary->total_fundraising_target ?? 0),
                'totalFundraisingRaised' => (float) ($fundraisingSummary->total_fundraising_raised ?? 0),
                'totalAssets' => (float) ($viewStats['total_system_balance'] ?? $totalSavings),
                'totalSavings' => (float) ($viewStats['total_system_balance'] ?? $totalSavings),
            ];
        });

        $recentMembers = Cache::remember('admin_dashboard:recent_members:v1', now()->addSeconds(30), static function () {
            return Member::query()
                ->select('id', 'user_id', 'member_number', 'full_name', 'profile_picture', 'created_at')
                ->latest()
                ->take(5)
                ->get()
                ->each
                ->append(['member_id']);
        });

        $recentLoans = Cache::remember('admin_dashboard:recent_loans:v2', now()->addSeconds(30), static function () {
            return Loan::query()->with(['member', 'statusRelation'])->latest()->take(5)->get();
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
                    ->selectRaw('YEAR(created_at) as period, COUNT(*) as total, COALESCE(SUM(principal_amount), 0) as total_amount')
                    ->groupBy('period')
                    ->get(),
                'period'
            );

            $transactionRows = $this->toMap(
                DB::table('transactions')
                    ->join('transaction_types', 'transactions.transaction_type_id', '=', 'transaction_types.id')
                    ->selectRaw('YEAR(transactions.created_at) as period, COUNT(*) as total, COALESCE(SUM(CASE WHEN transaction_types.name = "deposit" THEN transactions.amount ELSE 0 END), 0) as total_revenue, COALESCE(SUM(CASE WHEN transaction_types.name = "withdrawal" THEN transactions.amount ELSE 0 END), 0) as total_expenses')
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
                ->selectRaw('MONTH(created_at) as period, COUNT(*) as total, COALESCE(SUM(principal_amount), 0) as total_amount')
                ->groupBy('period')
                ->get(),
            'period'
        );

        $transactionRows = $this->toMap(
            DB::table('transactions')
                ->join('transaction_types', 'transactions.transaction_type_id', '=', 'transaction_types.id')
                ->whereYear('transactions.created_at', $selectedYear)
                ->selectRaw('MONTH(transactions.created_at) as period, COUNT(*) as total, COALESCE(SUM(CASE WHEN transaction_types.name = "deposit" THEN transactions.amount ELSE 0 END), 0) as total_revenue, COALESCE(SUM(CASE WHEN transaction_types.name = "withdrawal" THEN transactions.amount ELSE 0 END), 0) as total_expenses')
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
                    ->selectRaw('COUNT(*) as total_members')
                    ->first();

                $loanSummary = (clone $loanScope)
                    ->selectRaw('COUNT(*) as total_loans, COALESCE(SUM(principal_amount), 0) as total_loan_amount')
                    ->first();

                $pendingStatusId = LoanStatus::query()->where('name', 'pending')->value('id');
                $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');
                $pendingLoans = $pendingStatusId ? (clone $loanScope)->where('status_id', $pendingStatusId)->count() : 0;
                $approvedLoans = $approvedStatusId ? (clone $loanScope)->where('status_id', $approvedStatusId)->count() : 0;
                $totalSavings = (float) DB::table('savings_accounts')->sum('current_balance');

                $monthly['stats'] = [
                    'totalMembers' => (int) ($memberSummary->total_members ?? 0),
                    'totalLoans' => (int) ($loanSummary->total_loans ?? 0),
                    'totalProjects' => (clone $projectScope)->count(),
                    'activeFundraisings' => (clone $fundraisingScope)->where('status_id', FundraisingStatus::query()->where('name', 'active')->value('id'))->count(),
                    'approvedLoans' => (int) $approvedLoans,
                    'pendingLoans' => (int) $pendingLoans,
                    'totalSavings' => (float) $totalSavings,
                    'totalLoanAmount' => (float) ($loanSummary->total_loan_amount ?? 0),
                    'totalAssets' => (float) $totalSavings,
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
