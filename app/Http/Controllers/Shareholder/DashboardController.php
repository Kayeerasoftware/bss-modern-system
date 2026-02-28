<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Dividend;
use App\Models\InvestmentOpportunity;
use App\Models\Loan;
use App\Models\Member;
use App\Models\PortfolioPerformance;
use App\Models\Project;
use App\Models\Share;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $member = Member::query()->where('user_id', $user->id)->first();

        if (!$member) {
            return redirect()->route('shareholder.profile')
                ->with('error', 'Shareholder profile not found. Please update your profile first.');
        }

        $selectedYear = $this->normalizeYear((string) $request->query('year', (string) now()->year));
        $dashboardData = $this->buildDashboardData($member, $selectedYear);

        return view('shareholder.dashboard', [
            'member' => $member,
            'selectedYear' => $selectedYear,
            'dashboardData' => $dashboardData,
            'stats' => $dashboardData['stats'],
        ]);
    }

    public function getData(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $member = Member::query()->where('user_id', $user->id)->first();

            if (!$member) {
                return response()->json(['error' => 'Shareholder profile not found.'], 404);
            }

            $year = $this->normalizeYear((string) $request->query('year', (string) now()->year));
            $data = $this->buildDashboardData($member, $year);

            return response()->json($data);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function buildDashboardData(Member $member, string $year): array
    {
        $cacheKey = sprintf('shareholder_dashboard:%s:%s:v2', $member->id, $year);

        return Cache::remember($cacheKey, now()->addSeconds(90), function () use ($member, $year) {
            $periodConfig = $this->getPeriodConfig($year);
            $labels = $periodConfig['labels'];
            $periods = $periodConfig['periods'];

            $shareQuery = $this->sharesQuery($member);
            $dividendQuery = $this->dividendQuery($member);
            $transactionQuery = $this->transactionQuery($member);
            $performanceQuery = $this->performanceQuery($member);
            $loanQuery = $this->loanQuery($member);

            $shareValueSeries = $this->aggregateByPeriod(
                clone $shareQuery,
                'COALESCE(purchase_date, created_at)',
                'shares_owned * share_value',
                $year,
                $periods
            );
            $shareUnitsSeries = $this->aggregateByPeriod(
                clone $shareQuery,
                'COALESCE(purchase_date, created_at)',
                'shares_owned',
                $year,
                $periods
            );
            $cumulativeShareUnits = $this->cumulativeSeries($shareUnitsSeries);

            $dividendPaidSeries = $this->aggregateByPeriod(
                (clone $dividendQuery)->where('status', 'paid'),
                'COALESCE(payment_date, created_at)',
                'amount',
                $year,
                $periods
            );
            $dividendPendingSeries = $this->aggregateByPeriod(
                (clone $dividendQuery)->where('status', 'pending'),
                'COALESCE(payment_date, created_at)',
                'amount',
                $year,
                $periods
            );

            $depositSeries = $this->aggregateByPeriod(
                (clone $transactionQuery)->where('type', 'deposit'),
                'created_at',
                'amount',
                $year,
                $periods
            );
            $withdrawalSeries = $this->aggregateByPeriod(
                (clone $transactionQuery)->where('type', 'withdrawal'),
                'created_at',
                'amount',
                $year,
                $periods
            );
            $loanPaymentSeries = $this->aggregateByPeriod(
                (clone $transactionQuery)->whereIn('type', ['loan_payment', 'repayment']),
                'created_at',
                'amount',
                $year,
                $periods
            );
            $netCashSeries = $this->deriveNetCashSeries($depositSeries, $withdrawalSeries, $loanPaymentSeries);

            $portfolioValueSeries = $this->latestValueByPeriod(
                clone $performanceQuery,
                'period',
                'portfolio_value',
                $year,
                $periods
            );
            $performancePctSeries = $this->latestValueByPeriod(
                clone $performanceQuery,
                'period',
                'performance_percentage',
                $year,
                $periods
            );
            $projectRoiSeries = $this->aggregateByPeriod(
                Project::query(),
                'created_at',
                'COALESCE(roi, 0)',
                $year,
                $periods,
                'avg'
            );

            $totalShareUnits = (int) (clone $shareQuery)->sum('shares_owned');
            $shareValue = (float) (clone $shareQuery)->selectRaw('COALESCE(SUM(shares_owned * share_value), 0) as total')->value('total');
            $latestPortfolioValue = (float) ((clone $performanceQuery)->orderByDesc('period')->value('portfolio_value') ?? 0);
            $latestPerformancePct = (float) ((clone $performanceQuery)->orderByDesc('period')->value('performance_percentage') ?? 0);

            $dividendsPaid = (float) (clone $dividendQuery)->where('status', 'paid')->sum('amount');
            $dividendsPending = (float) (clone $dividendQuery)->where('status', 'pending')->sum('amount');
            $dividendsTotal = $dividendsPaid + $dividendsPending;
            $dividendsYtd = (float) (clone $dividendQuery)
                ->where('status', 'paid')
                ->whereRaw('YEAR(COALESCE(payment_date, created_at)) = ?', [now()->year])
                ->sum('amount');

            $trailingYearStart = now()->subMonths(12);
            $dividendsTrailingYear = (float) (clone $dividendQuery)
                ->where('status', 'paid')
                ->whereRaw('COALESCE(payment_date, created_at) >= ?', [$trailingYearStart])
                ->sum('amount');

            $savingsBalance = (float) ($member->savings_balance ?? $member->savings ?? 0);
            $loanOutstanding = (float) ($member->loan ?? 0);
            $portfolioValue = $latestPortfolioValue > 0 ? $latestPortfolioValue : ($shareValue + $savingsBalance);
            $netWorth = $shareValue + $savingsBalance + $dividendsPending - $loanOutstanding;
            $averageSharePrice = $totalShareUnits > 0 ? ($shareValue / $totalShareUnits) : 0;
            $dividendYieldPct = $shareValue > 0 ? (($dividendsTrailingYear / $shareValue) * 100) : 0;

            $deposits30 = (float) (clone $transactionQuery)->where('type', 'deposit')->where('created_at', '>=', now()->subDays(30))->sum('amount');
            $withdrawals30 = (float) (clone $transactionQuery)->where('type', 'withdrawal')->where('created_at', '>=', now()->subDays(30))->sum('amount');
            $loanPayments30 = (float) (clone $transactionQuery)->whereIn('type', ['loan_payment', 'repayment'])->where('created_at', '>=', now()->subDays(30))->sum('amount');
            $netCashFlow30 = $deposits30 - $withdrawals30 - $loanPayments30;

            $activeLoans = (int) (clone $loanQuery)->whereIn('status', ['approved', 'pending'])->count();
            $recentTransactions = (int) (clone $transactionQuery)->where('created_at', '>=', now()->subDays(30))->count();

            $activeProjects = (int) Project::query()->where('status', 'active')->count();
            $completedProjects = (int) Project::query()->where(function (Builder $query): void {
                $query->where('status', 'completed')->orWhere('progress', '>=', 100);
            })->count();
            $totalProjects = (int) Project::query()->count();
            $avgProjectRoi = (float) (Project::query()->avg('roi') ?? 0);

            $activeOpportunities = (int) InvestmentOpportunity::query()->where('status', 'active')->count();
            $opportunityTarget = (float) InvestmentOpportunity::query()->where('status', 'active')->sum('target_amount');

            $portfolioChangePct = $this->percentageChange($portfolioValueSeries);
            $insights = $this->buildInsights($labels, $netCashSeries, $dividendPaidSeries, $portfolioValueSeries);

            $stats = [
                'portfolioValue' => $portfolioValue,
                'portfolioChangePct' => $portfolioChangePct,
                'performanceRate' => $latestPerformancePct,
                'totalShares' => $totalShareUnits,
                'shareValue' => $shareValue,
                'averageSharePrice' => $averageSharePrice,
                'dividendsTotal' => $dividendsTotal,
                'dividendsPaid' => $dividendsPaid,
                'dividendsPending' => $dividendsPending,
                'dividendsYtd' => $dividendsYtd,
                'dividendYieldPct' => $dividendYieldPct,
                'savingsBalance' => $savingsBalance,
                'loanOutstanding' => $loanOutstanding,
                'netWorth' => $netWorth,
                'deposits30' => $deposits30,
                'withdrawals30' => $withdrawals30,
                'loanPayments30' => $loanPayments30,
                'netCashFlow30' => $netCashFlow30,
                'activeLoans' => $activeLoans,
                'recentTransactions' => $recentTransactions,
                'activeProjects' => $activeProjects,
                'completedProjects' => $completedProjects,
                'totalProjects' => $totalProjects,
                'avgProjectRoi' => $avgProjectRoi,
                'activeOpportunities' => $activeOpportunities,
                'opportunityTarget' => $opportunityTarget,
            ];

            return [
                'year' => $year,
                'labels' => $labels,
                'stats' => $stats,
                'charts' => [
                    'portfolioTrend' => [
                        'shareValue' => $shareValueSeries,
                        'portfolioValue' => $portfolioValueSeries,
                        'dividendsPaid' => $dividendPaidSeries,
                        'performancePct' => $performancePctSeries,
                    ],
                    'cashFlow' => [
                        'deposits' => $depositSeries,
                        'withdrawals' => $withdrawalSeries,
                        'loanPayments' => $loanPaymentSeries,
                        'net' => $netCashSeries,
                    ],
                    'dividendSplit' => [
                        'paid' => $dividendPaidSeries,
                        'pending' => $dividendPendingSeries,
                    ],
                    'shares' => [
                        'purchases' => $shareUnitsSeries,
                        'cumulative' => $cumulativeShareUnits,
                    ],
                    'projectSignals' => [
                        'roi' => $projectRoiSeries,
                    ],
                    'assetAllocation' => [
                        'labels' => ['Shares', 'Savings', 'Pending Dividends', 'Loans'],
                        'values' => [
                            $shareValue,
                            $savingsBalance,
                            $dividendsPending,
                            max($loanOutstanding, 0),
                        ],
                    ],
                    'projectSummary' => [
                        'labels' => ['Active Projects', 'Completed Projects', 'Other Projects', 'Active Opportunities'],
                        'values' => [
                            $activeProjects,
                            $completedProjects,
                            max($totalProjects - $activeProjects - $completedProjects, 0),
                            $activeOpportunities,
                        ],
                    ],
                    'riskSummary' => [
                        'labels' => ['Low Risk', 'Medium Risk', 'High Risk'],
                        'values' => [
                            (int) InvestmentOpportunity::query()->where('status', 'active')->where('risk_level', 'low')->count(),
                            (int) InvestmentOpportunity::query()->where('status', 'active')->where('risk_level', 'medium')->count(),
                            (int) InvestmentOpportunity::query()->where('status', 'active')->where('risk_level', 'high')->count(),
                        ],
                    ],
                ],
                'insights' => $insights,
                'recent' => [
                    'dividends' => $this->recentDividends($dividendQuery),
                    'shares' => $this->recentShares($shareQuery),
                    'transactions' => $this->recentTransactions($transactionQuery),
                    'projects' => Project::query()->latest()->take(6)->get(['id', 'name', 'status', 'progress', 'roi', 'budget', 'created_at']),
                    'opportunities' => InvestmentOpportunity::query()
                        ->where('status', 'active')
                        ->latest()
                        ->take(6)
                        ->get(['id', 'title', 'expected_roi', 'risk_level', 'deadline', 'target_amount', 'minimum_investment']),
                ],
            ];
        });
    }

    private function getPeriodConfig(string $year): array
    {
        if ($year === 'all') {
            $startYear = 2023;
            $currentYear = (int) now()->year;
            $periods = range($startYear, $currentYear);
            $labels = array_map(static fn (int $period): string => (string) $period, $periods);

            return ['periods' => $periods, 'labels' => $labels, 'mode' => 'year'];
        }

        $periods = range(1, 12);
        $labels = array_map(static fn (int $month): string => Carbon::create((int) $year, $month, 1)->format('M'), $periods);

        return ['periods' => $periods, 'labels' => $labels, 'mode' => 'month'];
    }

    private function sharesQuery(Member $member): Builder
    {
        return Share::query()->whereIn('member_id', $this->shareIdentifiers($member));
    }

    private function dividendQuery(Member $member): Builder
    {
        return Dividend::query()->whereIn('member_id', $this->memberIdentifiers($member));
    }

    private function transactionQuery(Member $member): Builder
    {
        return Transaction::query()->whereIn('member_id', $this->memberIdentifiers($member));
    }

    private function performanceQuery(Member $member): Builder
    {
        return PortfolioPerformance::query()->whereIn('member_id', $this->memberIdentifiers($member));
    }

    private function loanQuery(Member $member): Builder
    {
        return Loan::query()->whereIn('member_id', $this->memberIdentifiers($member));
    }

    private function memberIdentifiers(Member $member): array
    {
        $identifiers = [];

        if (!empty($member->member_id)) {
            $identifiers[] = (string) $member->member_id;
        }

        if (!empty($member->id)) {
            $identifiers[] = (string) $member->id;
        }

        $digits = null;
        if (preg_match('/(\d{1,8})$/', (string) ($member->member_id ?? ''), $matches)) {
            $digits = ltrim($matches[1], '0');
            if ($digits === '') {
                $digits = '0';
            }
        }

        if ($digits !== null) {
            $number = (int) $digits;
            if ($number > 0) {
                $padded4 = str_pad((string) $number, 4, '0', STR_PAD_LEFT);
                $padded6 = str_pad((string) $number, 6, '0', STR_PAD_LEFT);
                $identifiers[] = 'BSS' . $padded4;
                $identifiers[] = 'BSS-C15-' . $padded4;
                $identifiers[] = 'MEM' . $padded6;
            }
        }

        return array_values(array_unique(array_filter($identifiers)));
    }

    private function shareIdentifiers(Member $member): array
    {
        $identifiers = array_merge(
            [$member->id, (string) $member->id],
            $this->memberIdentifiers($member)
        );

        return array_values(array_unique(array_filter($identifiers, static fn ($identifier) => $identifier !== null && $identifier !== '')));
    }

    private function aggregateByPeriod(
        Builder $query,
        string $dateExpression,
        string $valueExpression,
        string $year,
        array $periods,
        string $aggregate = 'sum'
    ): array {
        $aggregation = strtolower($aggregate) === 'avg' ? 'AVG' : 'SUM';
        $periodExpression = $year === 'all' ? "YEAR($dateExpression)" : "MONTH($dateExpression)";

        $query->whereRaw("$dateExpression IS NOT NULL");
        if ($year !== 'all') {
            $query->whereRaw("YEAR($dateExpression) = ?", [(int) $year]);
        }

        $rows = $query->selectRaw("$periodExpression as period, COALESCE($aggregation($valueExpression), 0) as total")
            ->groupBy('period')
            ->pluck('total', 'period');

        $series = [];
        foreach ($periods as $period) {
            $series[] = (float) ($rows[$period] ?? 0);
        }

        return $series;
    }

    private function latestValueByPeriod(
        Builder $query,
        string $dateColumn,
        string $valueColumn,
        string $year,
        array $periods
    ): array {
        if ($year !== 'all') {
            $query->whereYear($dateColumn, (int) $year);
        }

        $rows = $query->whereNotNull($dateColumn)
            ->orderBy($dateColumn)
            ->get([$dateColumn, $valueColumn]);

        $indexed = [];
        foreach ($rows as $row) {
            $date = Carbon::parse($row->{$dateColumn});
            $period = $year === 'all' ? (int) $date->format('Y') : (int) $date->format('n');
            $indexed[$period] = (float) ($row->{$valueColumn} ?? 0);
        }

        $series = [];
        foreach ($periods as $period) {
            $series[] = (float) ($indexed[$period] ?? 0);
        }

        return $series;
    }

    private function cumulativeSeries(array $series): array
    {
        $running = 0;
        $cumulative = [];

        foreach ($series as $value) {
            $running += (float) $value;
            $cumulative[] = $running;
        }

        return $cumulative;
    }

    private function deriveNetCashSeries(array $deposits, array $withdrawals, array $loanPayments): array
    {
        $net = [];
        $count = max(count($deposits), count($withdrawals), count($loanPayments));

        for ($index = 0; $index < $count; $index++) {
            $net[] = ((float) ($deposits[$index] ?? 0))
                - ((float) ($withdrawals[$index] ?? 0))
                - ((float) ($loanPayments[$index] ?? 0));
        }

        return $net;
    }

    private function percentageChange(array $series): float
    {
        $values = array_values(array_filter($series, static fn ($value): bool => (float) $value > 0));
        if (count($values) < 2) {
            return 0.0;
        }

        $first = (float) $values[0];
        $last = (float) end($values);

        return $first > 0 ? (($last - $first) / $first) * 100 : 0.0;
    }

    private function buildInsights(array $labels, array $netCashSeries, array $dividendSeries, array $portfolioSeries): array
    {
        $bestNetIndex = $this->seriesIndexOfMax($netCashSeries);
        $worstNetIndex = $this->seriesIndexOfMin($netCashSeries);
        $bestDividendIndex = $this->seriesIndexOfMax($dividendSeries);
        $bestPortfolioIndex = $this->seriesIndexOfMax($portfolioSeries);

        $avgNetCash = count($netCashSeries) > 0 ? (array_sum($netCashSeries) / count($netCashSeries)) : 0;

        return [
            'bestCashFlowPeriod' => $bestNetIndex !== null ? ($labels[$bestNetIndex] ?? null) : null,
            'worstCashFlowPeriod' => $worstNetIndex !== null ? ($labels[$worstNetIndex] ?? null) : null,
            'bestDividendPeriod' => $bestDividendIndex !== null ? ($labels[$bestDividendIndex] ?? null) : null,
            'strongestPortfolioPeriod' => $bestPortfolioIndex !== null ? ($labels[$bestPortfolioIndex] ?? null) : null,
            'avgNetCashPerPeriod' => $avgNetCash,
        ];
    }

    private function seriesIndexOfMax(array $series): ?int
    {
        if ($series === []) {
            return null;
        }

        $maxValue = max($series);
        return array_search($maxValue, $series, true);
    }

    private function seriesIndexOfMin(array $series): ?int
    {
        if ($series === []) {
            return null;
        }

        $minValue = min($series);
        return array_search($minValue, $series, true);
    }

    private function recentDividends(Builder $query)
    {
        return $query->select('id', 'amount', 'status', 'payment_date', 'created_at', 'year', 'quarter')
            ->orderByRaw('COALESCE(payment_date, created_at) DESC')
            ->take(6)
            ->get();
    }

    private function recentShares(Builder $query)
    {
        return $query->select('id', 'shares_owned', 'share_value', 'purchase_date', 'created_at', 'certificate_number')
            ->orderByRaw('COALESCE(purchase_date, created_at) DESC')
            ->take(6)
            ->get();
    }

    private function recentTransactions(Builder $query)
    {
        return $query->select('id', 'transaction_id', 'type', 'amount', 'status', 'created_at', 'description')
            ->latest('created_at')
            ->take(8)
            ->get();
    }

    private function normalizeYear(string $year): string
    {
        if ($year === 'all') {
            return 'all';
        }

        $numericYear = (int) $year;
        if ($numericYear < 2023 || $numericYear > (int) now()->year + 1) {
            return (string) now()->year;
        }

        return (string) $numericYear;
    }
}
