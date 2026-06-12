<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Setting;
use App\Models\SavingsAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class SavingsController extends Controller
{
    public function index(Request $request)
    {
        $completedStatusId = DB::table('transaction_statuses')->where('name', 'completed')->value('id');
        $statusFilterId = null;
        if ($completedStatusId) {
            $completedCount = Transaction::query()->where('status_id', $completedStatusId)->count();
            if ($completedCount > 0) {
                $statusFilterId = $completedStatusId;
            }
        }

        $amountSql = 'COALESCE(NULLIF(transactions.net_amount, 0), transactions.amount, 0)';
        $depositCategories = ['savings_deposit', 'transfer_in', 'loan_disbursement'];
        $withdrawalCategories = ['savings_withdrawal', 'transfer_out', 'fundraising_transfer'];
        $categoryNames = array_values(array_unique(array_merge($depositCategories, $withdrawalCategories)));
        $depositCategorySql = "'" . implode("','", $depositCategories) . "'";
        $withdrawalCategorySql = "'" . implode("','", $withdrawalCategories) . "'";
        $excludeFundraising = function ($query): void {
            $query->whereNotExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('fundraising_contributions as fc')
                    ->whereColumn('fc.transaction_id', 'transactions.id');
            });
            $query->whereRaw("JSON_EXTRACT(transactions.metadata, '$.campaign_id') IS NULL");
        };

        $useCache = !$request->boolean('refresh');
        $statsTtl = now()->addMinutes(2);
        $lookupTtl = now()->addMinutes(30);
        $cachePrefix = 'cashier:savings:index:' . ($statusFilterId ?? 'all');
        $remember = function (string $suffix, $ttl, callable $callback) use ($useCache, $cachePrefix) {
            if (!$useCache) {
                return $callback();
            }

            return Cache::remember($cachePrefix . ':' . $suffix, $ttl, $callback);
        };

        $accountsBalancesSub = DB::table('savings_accounts')
            ->selectRaw('member_id, SUM(current_balance) as balance')
            ->groupBy('member_id');

        $txnBalancesSub = DB::table('transactions')
            ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
            ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
            ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
            ->where('ts.name', 'completed')
            ->whereIn('tc.name', $categoryNames)
            ->selectRaw("transactions.member_id, COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN {$amountSql} ELSE -{$amountSql} END), 0) as balance")
            ->groupBy('transactions.member_id');
        $excludeFundraising($txnBalancesSub);

        $combinedBalancesSub = DB::table('members')
            ->leftJoinSub($accountsBalancesSub, 'acc', 'members.id', '=', 'acc.member_id')
            ->leftJoinSub($txnBalancesSub, 'txn', 'members.id', '=', 'txn.member_id')
            ->selectRaw('members.id as member_id')
            ->selectRaw('acc.member_id as acc_member_id, txn.member_id as txn_member_id')
            ->selectRaw('COALESCE(acc.balance, 0) as account_balance')
            ->selectRaw('COALESCE(txn.balance, 0) as txn_balance')
            ->selectRaw("
                CASE
                    WHEN acc.member_id IS NOT NULL THEN acc.balance
                    WHEN txn.member_id IS NOT NULL THEN txn.balance
                    ELSE 0
                END as reported_balance
            ")
            ->selectRaw("
                CASE
                    WHEN acc.member_id IS NOT NULL THEN acc.balance
                    WHEN txn.member_id IS NOT NULL THEN txn.balance
                    ELSE 0
                END as balance
            ")
            ->selectRaw("
                CASE
                    WHEN acc.member_id IS NOT NULL THEN 'account'
                    WHEN txn.member_id IS NOT NULL THEN 'transaction'
                    ELSE 'none'
                END as balance_source
            ");

        $combinedBalancesBase = DB::query()->fromSub($combinedBalancesSub, 'balances');
        $balanceTotals = $remember('balance_totals', $statsTtl, function () use ($combinedBalancesBase) {
            return (clone $combinedBalancesBase)
                ->selectRaw('COALESCE(SUM(account_balance), 0) as total_account_balance')
                ->selectRaw('COALESCE(SUM(txn_balance), 0) as total_txn_balance')
                ->selectRaw('COALESCE(SUM(reported_balance), 0) as total_reported_balance')
                ->selectRaw("SUM(CASE WHEN reported_balance > 0 THEN 1 ELSE 0 END) as savers")
                ->first();
        });
        $balanceTotals = $balanceTotals ?: (object) [
            'total_account_balance' => 0,
            'total_txn_balance' => 0,
            'total_reported_balance' => 0,
            'savers' => 0,
        ];

        $totalSavingsBalanceAccounts = (float) $balanceTotals->total_account_balance;
        $totalSavingsBalanceTransactions = (float) $balanceTotals->total_txn_balance;

        $balanceSourceCounts = $remember('balance_source_counts', $statsTtl, function () use ($combinedBalancesBase) {
            return (clone $combinedBalancesBase)
                ->selectRaw("SUM(CASE WHEN balance_source = 'account' THEN 1 ELSE 0 END) as account")
                ->selectRaw("SUM(CASE WHEN balance_source = 'transaction' THEN 1 ELSE 0 END) as transaction")
                ->selectRaw("SUM(CASE WHEN reported_balance > 0 THEN 1 ELSE 0 END) as savers")
                ->first();
        });
        $balanceSourceCounts = $balanceSourceCounts ?: (object) [
            'account' => 0,
            'transaction' => 0,
            'savers' => 0,
        ];

        $reconciliationStats = $remember('reconciliation_stats', $statsTtl, function () use ($combinedBalancesBase) {
            return (clone $combinedBalancesBase)
                ->selectRaw("SUM(CASE WHEN acc_member_id IS NOT NULL THEN 1 ELSE 0 END) as accounts_present")
                ->selectRaw("SUM(CASE WHEN txn_member_id IS NOT NULL THEN 1 ELSE 0 END) as txn_present")
                ->selectRaw("SUM(CASE WHEN acc_member_id IS NOT NULL AND txn_member_id IS NOT NULL AND ABS(account_balance - txn_balance) > 0.01 THEN 1 ELSE 0 END) as acc_txn_mismatches")
                ->selectRaw("AVG(CASE WHEN acc_member_id IS NOT NULL AND txn_member_id IS NOT NULL THEN ABS(account_balance - txn_balance) END) as acc_txn_avg_gap")
                ->selectRaw("MAX(CASE WHEN acc_member_id IS NOT NULL AND txn_member_id IS NOT NULL THEN ABS(account_balance - txn_balance) END) as acc_txn_max_gap")
                ->first();
        });
        $reconciliationStats = $reconciliationStats ?: (object) [
            'accounts_present' => 0,
            'txn_present' => 0,
            'acc_txn_mismatches' => 0,
            'acc_txn_avg_gap' => 0,
            'acc_txn_max_gap' => 0,
        ];

        $savingsTotals = $remember('savings_totals', $statsTtl, function () use ($statusFilterId, $categoryNames, $depositCategorySql, $withdrawalCategorySql, $amountSql) {
            return DB::table('transactions')
                ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
                ->when($statusFilterId, fn ($q) => $q->where('transactions.status_id', $statusFilterId))
                ->whereIn('tc.name', $categoryNames)
                ->whereNotExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('fundraising_contributions as fc')
                        ->whereColumn('fc.transaction_id', 'transactions.id');
                })
                ->whereRaw("JSON_EXTRACT(transactions.metadata, '$.campaign_id') IS NULL")
                ->selectRaw("COALESCE(SUM(CASE WHEN tc.name IN ({$depositCategorySql}) THEN {$amountSql} ELSE 0 END), 0) as deposits")
                ->selectRaw("COALESCE(SUM(CASE WHEN tc.name IN ({$withdrawalCategorySql}) THEN {$amountSql} ELSE 0 END), 0) as withdrawals")
                ->first();
        });

        $savingsDeposits = (float) ($savingsTotals->deposits ?? 0);
        $savingsWithdrawals = (float) ($savingsTotals->withdrawals ?? 0);
        $savingsNet = $savingsDeposits - $savingsWithdrawals;
        $settingsInterestRate = (float) setting('interest_rate', 0);
        if ($settingsInterestRate < 0) {
            $settingsInterestRate = 0;
        }
        $accruedSavingsInterestProfit = (float) $remember('accrued_savings_interest_profit', $statsTtl, function () {
            return DB::table('savings_interest_accruals')
                ->whereIn('status', ['paid', 'accrued'])
                ->sum('net_amount');
        });
        $computedSavingsInterestProfit = ($balanceTotals->total_reported_balance ?? 0) > 0 && $settingsInterestRate > 0
            ? round(((float) $balanceTotals->total_reported_balance * $settingsInterestRate) / 100, 2)
            : 0.0;
        $useComputedSavingsInterest = $accruedSavingsInterestProfit <= 0 && $computedSavingsInterestProfit > 0;
        $totalSavingsInterestProfit = $useComputedSavingsInterest ? $computedSavingsInterestProfit : $accruedSavingsInterestProfit;
        $totalLoanInterestProfit = (float) $remember('total_loan_interest_profit', $statsTtl, function () {
            return DB::table('loan_repayments')
                ->sum('interest_applied');
        });
        $totalInterestProfit = $totalSavingsInterestProfit + $totalLoanInterestProfit;

        $completedSavingsTxCount = (int) $remember('completed_savings_tx_count', $statsTtl, function () {
            return DB::table('transactions')
                ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
                ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
                ->where('ts.name', 'completed')
                ->where('tt.affects_savings', 1)
                ->whereNotExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('fundraising_contributions as fc')
                        ->whereColumn('fc.transaction_id', 'transactions.id');
                })
                ->whereRaw("JSON_EXTRACT(transactions.metadata, '$.campaign_id') IS NULL")
                ->count();
        });

        $totalSavingsBalance = (float) $balanceTotals->total_reported_balance;
        $profitMargin = $totalSavingsBalance > 0 ? round(($totalInterestProfit / $totalSavingsBalance) * 100, 2) : 0.0;
        $accountsSummary = $remember('accounts_summary', $statsTtl, function () {
            $today = now()->toDateString();
            return SavingsAccount::query()
                ->selectRaw('COALESCE(SUM(available_balance), 0) as total_available_balance')
                ->selectRaw('COALESCE(SUM(overdraft_limit), 0) as total_overdraft_limit')
                ->selectRaw('COALESCE(SUM(overdraft_used), 0) as total_overdraft_used')
                ->selectRaw('COUNT(*) as total_accounts')
                ->selectRaw("SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_accounts")
                ->selectRaw("SUM(CASE WHEN status = 'frozen' THEN 1 ELSE 0 END) as frozen_accounts")
                ->selectRaw("SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_accounts")
                ->selectRaw("SUM(CASE WHEN is_joint = 1 THEN 1 ELSE 0 END) as joint_accounts")
                ->selectRaw("SUM(CASE WHEN maturity_date IS NOT NULL AND maturity_date <= ? AND status = 'active' THEN 1 ELSE 0 END) as matured_accounts", [$today])
                ->first();
        });
        $accountsSummary = $accountsSummary ?: (object) [
            'total_available_balance' => 0,
            'total_overdraft_limit' => 0,
            'total_overdraft_used' => 0,
            'total_accounts' => 0,
            'active_accounts' => 0,
            'frozen_accounts' => 0,
            'closed_accounts' => 0,
            'joint_accounts' => 0,
            'matured_accounts' => 0,
        ];

        $totalAvailableBalance = (float) $accountsSummary->total_available_balance;
        $totalOverdraftLimit = (float) $accountsSummary->total_overdraft_limit;
        $totalOverdraftUsed = (float) $accountsSummary->total_overdraft_used;
        $totalAccounts = (int) $accountsSummary->total_accounts;
        $totalMembersWithSavings = (int) $balanceTotals->savers;
        $avgBalance = $totalMembersWithSavings > 0 ? ($totalSavingsBalance / $totalMembersWithSavings) : 0;
        $activeAccounts = (int) $accountsSummary->active_accounts;
        $frozenAccounts = (int) $accountsSummary->frozen_accounts;
        $closedAccounts = (int) $accountsSummary->closed_accounts;
        $jointAccounts = (int) $accountsSummary->joint_accounts;
        $maturedAccounts = (int) $accountsSummary->matured_accounts;

        $statusBreakdown = $remember('status_breakdown', $statsTtl, function () {
            return SavingsAccount::query()
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->orderByDesc('count')
                ->get();
        });

        $accountStatuses = $remember('account_statuses', $statsTtl, function () {
            return SavingsAccount::query()
                ->select('status')
                ->whereNotNull('status')
                ->distinct()
                ->orderBy('status')
                ->pluck('status');
        });

        $useDerivedAccounts = $totalAccounts === 0;
        $derivedBalancesSub = null;
        if ($useDerivedAccounts) {
            $derivedBalancesSub = DB::table('transactions')
                ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
                ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
                ->where('ts.name', 'completed')
                ->where('tt.affects_savings', 1)
                ->selectRaw("transactions.member_id, COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN {$amountSql} ELSE -{$amountSql} END), 0) as balance")
                ->groupBy('transactions.member_id');
            $excludeFundraising($derivedBalancesSub);

            $totalSavingsBalance = (float) DB::query()->fromSub($combinedBalancesSub, 'balances')->sum('reported_balance');
            $totalAvailableBalance = $totalSavingsBalance;
            $totalOverdraftLimit = 0;
            $totalOverdraftUsed = 0;
            $totalAccounts = (int) DB::query()->fromSub($derivedBalancesSub, 'balances')->count();
            $avgBalance = $totalMembersWithSavings > 0 ? ($totalSavingsBalance / $totalMembersWithSavings) : 0;
            $activeAccounts = $totalMembersWithSavings;
            $frozenAccounts = 0;
            $closedAccounts = 0;
            $jointAccounts = 0;
            $maturedAccounts = 0;
            $statusBreakdown = collect([
                (object) ['status' => 'active', 'count' => $activeAccounts],
                (object) ['status' => 'inactive', 'count' => max(0, $totalAccounts - $activeAccounts)],
            ]);
            $accountStatuses = collect(['active', 'inactive']);
        }

        $last30dStart = now()->subDays(30);
        $last30dSavingsNet = $remember('last30d_savings_net', $statsTtl, function () use ($statusFilterId, $categoryNames, $depositCategorySql, $amountSql, $last30dStart) {
            return DB::table('transactions')
                ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
                ->when($statusFilterId, fn ($q) => $q->where('transactions.status_id', $statusFilterId))
                ->whereIn('tc.name', $categoryNames)
                ->whereNotExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('fundraising_contributions as fc')
                        ->whereColumn('fc.transaction_id', 'transactions.id');
                })
                ->whereRaw("JSON_EXTRACT(transactions.metadata, '$.campaign_id') IS NULL")
                ->whereRaw('COALESCE(transactions.transaction_date, transactions.created_at) >= ?', [$last30dStart])
                ->selectRaw("COALESCE(SUM(CASE WHEN tc.name IN ({$depositCategorySql}) THEN {$amountSql} ELSE -{$amountSql} END), 0) as net")
                ->value('net');
        });

        $monthlyRows = $remember('monthly_rows', $statsTtl, function () use ($statusFilterId, $categoryNames, $depositCategorySql, $withdrawalCategorySql, $amountSql) {
            return DB::table('transactions')
                ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
                ->when($statusFilterId, fn ($q) => $q->where('transactions.status_id', $statusFilterId))
                ->whereIn('tc.name', $categoryNames)
                ->whereNotExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('fundraising_contributions as fc')
                        ->whereColumn('fc.transaction_id', 'transactions.id');
                })
                ->whereRaw("JSON_EXTRACT(transactions.metadata, '$.campaign_id') IS NULL")
                ->whereRaw('COALESCE(transactions.transaction_date, transactions.created_at) >= ?', [now()->subMonths(12)])
                ->selectRaw("DATE_FORMAT(COALESCE(transactions.transaction_date, transactions.created_at), '%Y-%m') as month_key")
                ->selectRaw("COALESCE(SUM(CASE WHEN tc.name IN ({$depositCategorySql}) THEN {$amountSql} ELSE 0 END), 0) as deposits")
                ->selectRaw("COALESCE(SUM(CASE WHEN tc.name IN ({$withdrawalCategorySql}) THEN {$amountSql} ELSE 0 END), 0) as withdrawals")
                ->groupBy('month_key')
                ->get();
        });

        $monthlyMap = $monthlyRows->keyBy('month_key');
        $monthlyLabels = [];
        $monthlyDeposits = [];
        $monthlyWithdrawals = [];
        $monthlyNet = [];

        $monthStart = now()->subMonths(11)->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $month = $monthStart->copy()->addMonths($i);
            $key = $month->format('Y-m');
            $label = $month->format('M Y');
            $row = $monthlyMap->get($key);
            $deposit = (float) ($row->deposits ?? 0);
            $withdrawal = (float) ($row->withdrawals ?? 0);
            $monthlyLabels[] = $label;
            $monthlyDeposits[] = $deposit;
            $monthlyWithdrawals[] = $withdrawal;
            $monthlyNet[] = $deposit - $withdrawal;
        }

        $balancesSubAccounts = null;
        if ($totalAccounts > 0) {
            $balancesSubAccounts = DB::table('savings_accounts')
                ->selectRaw('member_id, SUM(current_balance) as balance')
                ->groupBy('member_id');
        }

        $balancesSubDerived = DB::table('transactions')
            ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
            ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
            ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
            ->where('ts.name', 'completed')
            ->whereIn('tc.name', $categoryNames)
            ->selectRaw("transactions.member_id, COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN {$amountSql} ELSE -{$amountSql} END), 0) as balance")
            ->groupBy('transactions.member_id');
        $excludeFundraising($balancesSubDerived);

        $balancesSub = $balancesSubAccounts ?? $balancesSubDerived;

        $accountsBalancesSub = DB::table('savings_accounts')
            ->selectRaw('member_id, SUM(current_balance) as balance')
            ->groupBy('member_id');

        $accountsCountSub = DB::table('savings_accounts')
            ->selectRaw('member_id, COUNT(*) as accounts_count')
            ->groupBy('member_id');

        $accountsNumbersSub = DB::table('savings_accounts')
            ->selectRaw("member_id, GROUP_CONCAT(account_number ORDER BY account_number SEPARATOR ', ') as account_numbers")
            ->groupBy('member_id');

        $derivedBalancesSub = $derivedBalancesSub
            ?? DB::table('transactions')
                ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
                ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
                ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
                ->where('ts.name', 'completed')
                ->whereIn('tc.name', $categoryNames)
                ->selectRaw("transactions.member_id, COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN {$amountSql} ELSE -{$amountSql} END), 0) as balance")
                ->groupBy('transactions.member_id');
        $excludeFundraising($derivedBalancesSub);

        $netSavingsSub = DB::table('transactions')
            ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
            ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
            ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
            ->where('ts.name', 'completed')
            ->whereIn('tc.name', $categoryNames)
            ->selectRaw("transactions.member_id, COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN {$amountSql} ELSE -{$amountSql} END), 0) as net_savings")
            ->groupBy('transactions.member_id');
        $excludeFundraising($netSavingsSub);

        $topMembersBase = Member::query()
            ->joinSub($combinedBalancesSub, 'combined_balances', 'members.id', '=', 'combined_balances.member_id')
            ->leftJoinSub($accountsCountSub, 'acc_counts', 'members.id', '=', 'acc_counts.member_id')
            ->leftJoinSub($accountsNumbersSub, 'acc_numbers', 'members.id', '=', 'acc_numbers.member_id')
            ->leftJoinSub($netSavingsSub, 'net_savings', 'members.id', '=', 'net_savings.member_id')
            ->select('members.*', 'combined_balances.balance')
            ->addSelect(DB::raw('COALESCE(acc_counts.accounts_count, 0) as accounts_count'))
            ->addSelect(DB::raw("COALESCE(acc_numbers.account_numbers, '') as account_numbers"))
            ->addSelect(DB::raw('COALESCE(net_savings.net_savings, 0) as net_savings'))
            ->where('combined_balances.balance', '>', 0)
            ->orderByDesc('combined_balances.balance');

        $topMembers = $topMembersBase
            ->paginate(25, ['*'], 'top_page')
            ->appends($request->except('top_page'));

        $topTenTotal = (float) $remember('top_ten_total', $statsTtl, function () use ($combinedBalancesSub) {
            return DB::query()
                ->fromSub($combinedBalancesSub, 'combined_balances')
                ->orderByDesc('reported_balance')
                ->limit(10)
                ->sum('reported_balance');
        });

        $topTenShare = $totalSavingsBalance > 0 ? round(($topTenTotal / $totalSavingsBalance) * 100, 1) : 0;
        $liquidityRatio = $totalSavingsBalance > 0 ? round(($totalAvailableBalance / $totalSavingsBalance) * 100, 1) : 0;
        $overdraftUtilization = $totalOverdraftLimit > 0 ? round(($totalOverdraftUsed / $totalOverdraftLimit) * 100, 1) : 0;
        $savingsVelocity = $totalSavingsBalance > 0 ? round(($last30dSavingsNet / $totalSavingsBalance) * 100, 2) : 0;

        $goalTargets = [100000, 250000, 500000, 1000000, 5000000];
        $goalCounts = $remember('goal_counts', $statsTtl, function () use ($combinedBalancesBase) {
            return (clone $combinedBalancesBase)
                ->selectRaw("SUM(CASE WHEN balance >= 100000 THEN 1 ELSE 0 END) as c1")
                ->selectRaw("SUM(CASE WHEN balance >= 250000 THEN 1 ELSE 0 END) as c2")
                ->selectRaw("SUM(CASE WHEN balance >= 500000 THEN 1 ELSE 0 END) as c3")
                ->selectRaw("SUM(CASE WHEN balance >= 1000000 THEN 1 ELSE 0 END) as c4")
                ->selectRaw("SUM(CASE WHEN balance >= 5000000 THEN 1 ELSE 0 END) as c5")
                ->first();
        });
        $goalCounts = $goalCounts ?: (object) ['c1' => 0, 'c2' => 0, 'c3' => 0, 'c4' => 0, 'c5' => 0];
        $goalProgress = [
            [
                'target' => $goalTargets[0],
                'members' => (int) $goalCounts->c1,
                'percent' => $totalMembersWithSavings > 0 ? round(($goalCounts->c1 / $totalMembersWithSavings) * 100, 1) : 0,
            ],
            [
                'target' => $goalTargets[1],
                'members' => (int) $goalCounts->c2,
                'percent' => $totalMembersWithSavings > 0 ? round(($goalCounts->c2 / $totalMembersWithSavings) * 100, 1) : 0,
            ],
            [
                'target' => $goalTargets[2],
                'members' => (int) $goalCounts->c3,
                'percent' => $totalMembersWithSavings > 0 ? round(($goalCounts->c3 / $totalMembersWithSavings) * 100, 1) : 0,
            ],
            [
                'target' => $goalTargets[3],
                'members' => (int) $goalCounts->c4,
                'percent' => $totalMembersWithSavings > 0 ? round(($goalCounts->c4 / $totalMembersWithSavings) * 100, 1) : 0,
            ],
            [
                'target' => $goalTargets[4],
                'members' => (int) $goalCounts->c5,
                'percent' => $totalMembersWithSavings > 0 ? round(($goalCounts->c5 / $totalMembersWithSavings) * 100, 1) : 0,
            ],
        ];

        $reconTolerance = (float) $request->input('recon_tolerance', 1000);
        if ($reconTolerance < 0) {
            $reconTolerance = 0;
        }

        $mismatchBase = DB::query()
            ->fromSub($combinedBalancesSub, 'balances')
            ->join('members', 'members.id', '=', 'balances.member_id')
            ->select(
                'members.id as member_id',
                'members.full_name',
                DB::raw('COALESCE(members.member_account_number, members.member_number) as member_number'),
                'members.profile_picture',
                'balances.account_balance',
                'balances.txn_balance',
                'balances.reported_balance',
                'balances.balance_source'
            )
            ->selectRaw('ABS(COALESCE(balances.account_balance, 0) - COALESCE(balances.txn_balance, 0)) as max_gap')
            ->where(function ($q) use ($reconTolerance) {
                $q->whereRaw('balances.acc_member_id IS NOT NULL AND balances.txn_member_id IS NOT NULL AND ABS(balances.account_balance - balances.txn_balance) > ?', [$reconTolerance]);
            });

        $mismatchCount = (int) (clone $mismatchBase)->count();
        $mismatches = (clone $mismatchBase)
            ->orderByDesc('max_gap')
            ->limit(15)
            ->get()
            ->map(function ($row) {
                $row->profile_picture_url = $this->resolveMemberPictureUrl($row->profile_picture ?? null);
                return $row;
            });

        $lowBalanceThreshold = (float) $request->input('low_balance_threshold', 50000);
        $largeWithdrawalThreshold = (float) $request->input('large_withdrawal_threshold', 500000);
        $largeWithdrawalDays = (int) $request->input('large_withdrawal_days', 7);
        if ($largeWithdrawalDays < 1) {
            $largeWithdrawalDays = 1;
        }
        if ($largeWithdrawalDays > 90) {
            $largeWithdrawalDays = 90;
        }

        if (!$useDerivedAccounts) {
            $lowBalanceAccounts = SavingsAccount::query()
                ->with('member:id,full_name,member_account_number,member_number,profile_picture')
                ->where('current_balance', '<=', $lowBalanceThreshold)
                ->orderBy('current_balance')
                ->limit(10)
                ->get();
        } else {
            $derivedBalancesSub = $derivedBalancesSub
                ?? DB::table('transactions')
                    ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
                    ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
                    ->where('ts.name', 'completed')
                    ->where('tt.affects_savings', 1)
                    ->selectRaw("transactions.member_id, COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN {$amountSql} ELSE -{$amountSql} END), 0) as balance")
                    ->groupBy('transactions.member_id');
            $excludeFundraising($derivedBalancesSub);

            $lowBalanceAccounts = DB::table('members')
                ->leftJoinSub($derivedBalancesSub, 'balances', 'members.id', '=', 'balances.member_id')
                ->select(
                    'members.id as member_id',
                    'members.full_name',
                    'members.member_account_number',
                    DB::raw('COALESCE(members.member_account_number, members.member_number) as member_number'),
                    'members.profile_picture',
                    DB::raw('COALESCE(balances.balance, 0) as current_balance')
                )
                ->whereRaw('COALESCE(balances.balance, 0) <= ?', [$lowBalanceThreshold])
                ->orderBy('current_balance')
                ->limit(10)
                ->get()
                ->map(function ($row) {
                    $derivedAccountNumber = 'SAV-BSS-C15-' . str_pad((string) $row->member_id, 4, '0', STR_PAD_LEFT);
                    return (object) [
                        'member_id' => $row->member_id,
                        'current_balance' => (float) $row->current_balance,
                        'member' => (object) [
                            'full_name' => $row->full_name,
                            'member_number' => $row->member_account_number ?? $row->member_number,
                            'profile_picture_url' => $this->resolveMemberPictureUrl($row->profile_picture ?? null),
                        ],
                        'account_number' => $derivedAccountNumber,
                    ];
                });
        }

        $largeWithdrawals = Transaction::query()
            ->with(['member', 'transactionCategory', 'statusRelation'])
            ->when($statusFilterId, fn ($q) => $q->where('status_id', $statusFilterId))
            ->whereHas('transactionCategory', fn ($q) => $q->whereIn('name', $withdrawalCategories))
            ->whereNotExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('fundraising_contributions as fc')
                    ->whereColumn('fc.transaction_id', 'transactions.id');
            })
            ->whereRaw("JSON_EXTRACT(transactions.metadata, '$.campaign_id') IS NULL")
            ->whereRaw('COALESCE(transaction_date, created_at) >= ?', [now()->subDays($largeWithdrawalDays)])
            ->whereRaw("{$amountSql} >= ?", [$largeWithdrawalThreshold])
            ->orderByDesc(DB::raw($amountSql))
            ->limit(10)
            ->get();

        $lastSavingsDates = DB::table('transactions')
            ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
            ->when($statusFilterId, fn ($q) => $q->where('transactions.status_id', $statusFilterId))
            ->whereIn('tc.name', $categoryNames)
            ->whereNotExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('fundraising_contributions as fc')
                    ->whereColumn('fc.transaction_id', 'transactions.id');
            })
            ->whereRaw("JSON_EXTRACT(transactions.metadata, '$.campaign_id') IS NULL")
            ->selectRaw('transactions.member_id, MAX(COALESCE(transactions.transaction_date, transactions.created_at)) as last_savings_date')
            ->groupBy('transactions.member_id')
            ->pluck('last_savings_date', 'member_id');

        $accounts = null;
        if ($totalAccounts > 0) {
            $hasAccountFilters = $request->filled('accounts_search')
                || $request->filled('accounts_status')
                || $request->filled('accounts_joint')
                || $request->filled('accounts_min')
                || $request->filled('accounts_max')
                || $request->filled('accounts_opened_from')
                || $request->filled('accounts_opened_to')
                || $request->filled('accounts_maturity_from')
                || $request->filled('accounts_maturity_to');

            $accountsQuery = SavingsAccount::query()
                ->with('member:id,full_name,member_account_number,member_number,profile_picture')
                ->when($request->filled('accounts_search'), function ($q) use ($request) {
                    $term = '%' . $request->input('accounts_search') . '%';
                    $q->where(function ($inner) use ($term) {
                        $inner->where('account_number', 'like', $term)
                            ->orWhere('account_name', 'like', $term)
                            ->orWhereHas('member', fn ($m) => $m->where('full_name', 'like', $term)
                                ->orWhere('member_account_number', 'like', $term)
                                ->orWhere('member_number', 'like', $term));
                    });
                })
                ->when($request->filled('accounts_status'), fn ($q) => $q->where('status', $request->input('accounts_status')))
                ->when($request->filled('accounts_joint'), fn ($q) => $q->where('is_joint', $request->input('accounts_joint') === 'yes' ? 1 : 0))
                ->when($request->filled('accounts_min'), fn ($q) => $q->where('current_balance', '>=', $request->input('accounts_min')))
                ->when($request->filled('accounts_max'), fn ($q) => $q->where('current_balance', '<=', $request->input('accounts_max')))
                ->when($request->filled('accounts_opened_from'), fn ($q) => $q->whereDate('opening_date', '>=', $request->input('accounts_opened_from')))
                ->when($request->filled('accounts_opened_to'), fn ($q) => $q->whereDate('opening_date', '<=', $request->input('accounts_opened_to')))
                ->when($request->filled('accounts_maturity_from'), fn ($q) => $q->whereDate('maturity_date', '>=', $request->input('accounts_maturity_from')))
                ->when($request->filled('accounts_maturity_to'), fn ($q) => $q->whereDate('maturity_date', '<=', $request->input('accounts_maturity_to')))
                ->orderByDesc('current_balance');

            $totalMembers = (int) DB::table('members')->count();
            if (!$hasAccountFilters && $totalAccounts < $totalMembers) {
                $realAccounts = $accountsQuery->get();
                $existingMemberIds = $realAccounts->pluck('member_id')->unique()->values();

                $derivedBalancesSub = $derivedBalancesSub
                    ?? DB::table('transactions')
                        ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
                        ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
                        ->where('ts.name', 'completed')
                        ->where('tt.affects_savings', 1)
                        ->selectRaw("transactions.member_id, COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN {$amountSql} ELSE -{$amountSql} END), 0) as balance")
                        ->groupBy('transactions.member_id');
                $excludeFundraising($derivedBalancesSub);

                $missingMembers = DB::table('members')
                    ->whereNotIn('members.id', $existingMemberIds)
                    ->leftJoinSub($derivedBalancesSub, 'balances', 'members.id', '=', 'balances.member_id')
                    ->select(
                        'members.id as member_id',
                        'members.full_name',
                        'members.member_account_number',
                        DB::raw('COALESCE(members.member_account_number, members.member_number) as member_number'),
                        'members.profile_picture',
                        'members.join_date',
                        'members.updated_at',
                        DB::raw('COALESCE(balances.balance, 0) as current_balance')
                    )
                    ->get()
                    ->map(function ($row) {
                        $balance = (float) $row->current_balance;
                        $createdAt = $row->join_date ?? $row->updated_at ?? now();
                        $derivedAccountNumber = 'SAV-BSS-C15-' . str_pad((string) $row->member_id, 4, '0', STR_PAD_LEFT);
                        return (object) [
                            'member_id' => $row->member_id,
                            'account_number' => $derivedAccountNumber,
                            'account_name' => 'Member Savings',
                            'current_balance' => $balance,
                            'available_balance' => $balance,
                            'overdraft_used' => 0,
                            'overdraft_limit' => 0,
                            'accrued_interest' => 0,
                            'last_interest_calculation' => null,
                            'status' => $balance > 0 ? 'active' : 'inactive',
                            'opening_date' => $row->join_date,
                            'maturity_date' => null,
                            'updated_at' => $row->updated_at,
                            'created_at' => $createdAt,
                            'is_joint' => 0,
                            'member' => (object) [
                                'full_name' => $row->full_name,
                                'member_number' => $row->member_account_number ?? $row->member_number,
                                'profile_picture_url' => $this->resolveMemberPictureUrl($row->profile_picture ?? null),
                            ],
                        ];
                    });

                $accountsCollection = $realAccounts->concat($missingMembers)->sortByDesc('current_balance')->values();
                $perPage = 15;
                $page = max(1, (int) $request->input('accounts_page', 1));
                $slice = $accountsCollection->slice(($page - 1) * $perPage, $perPage)->values();
                $accounts = new LengthAwarePaginator(
                    $slice,
                    $accountsCollection->count(),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            } else {
                $accounts = $accountsQuery
                    ->paginate(15, ['*'], 'accounts_page')
                    ->appends($request->except('accounts_page'));
            }

            if ($accounts->total() === 0) {
                $accountStatuses = collect(['active', 'inactive']);

                $derivedBalancesSub = $derivedBalancesSub
                    ?? DB::table('transactions')
                        ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
                        ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
                        ->where('ts.name', 'completed')
                        ->where('tt.affects_savings', 1)
                        ->selectRaw("transactions.member_id, COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN {$amountSql} ELSE -{$amountSql} END), 0) as balance")
                        ->groupBy('transactions.member_id');

                $accountsQuery = DB::table('members')
                    ->leftJoinSub($derivedBalancesSub, 'balances', 'members.id', '=', 'balances.member_id')
                    ->select(
                        'members.id as member_id',
                        'members.full_name',
                        'members.member_account_number',
                        DB::raw('COALESCE(members.member_account_number, members.member_number) as member_number'),
                        'members.profile_picture',
                        'members.join_date',
                        'members.updated_at',
                        DB::raw('COALESCE(balances.balance, 0) as current_balance')
                    );

                if ($request->filled('accounts_search')) {
                    $term = '%' . $request->input('accounts_search') . '%';
                    $accountsQuery->where(function ($q) use ($term) {
                        $q->where('members.full_name', 'like', $term)
                            ->orWhere('members.member_account_number', 'like', $term)
                            ->orWhere('members.member_number', 'like', $term);
                    });
                }

                if ($request->filled('accounts_status')) {
                    $status = $request->input('accounts_status');
                    if ($status === 'active') {
                        $accountsQuery->whereRaw('COALESCE(balances.balance, 0) > 0');
                    } elseif ($status === 'inactive') {
                        $accountsQuery->whereRaw('COALESCE(balances.balance, 0) <= 0');
                    }
                }

                if ($request->filled('accounts_min')) {
                    $accountsQuery->whereRaw('COALESCE(balances.balance, 0) >= ?', [$request->input('accounts_min')]);
                }
                if ($request->filled('accounts_max')) {
                    $accountsQuery->whereRaw('COALESCE(balances.balance, 0) <= ?', [$request->input('accounts_max')]);
                }
                if ($request->filled('accounts_opened_from')) {
                    $accountsQuery->whereDate('members.join_date', '>=', $request->input('accounts_opened_from'));
                }
                if ($request->filled('accounts_opened_to')) {
                    $accountsQuery->whereDate('members.join_date', '<=', $request->input('accounts_opened_to'));
                }
                if ($request->filled('accounts_joint')) {
                    if ($request->input('accounts_joint') === 'yes') {
                        $accountsQuery->whereRaw('1 = 0');
                    }
                }
                if ($request->filled('accounts_maturity_from') || $request->filled('accounts_maturity_to')) {
                    $accountsQuery->whereRaw('1 = 0');
                }

                $accountsQuery->orderByDesc('current_balance');

                $accounts = $accountsQuery
                    ->paginate(15, ['*'], 'accounts_page')
                    ->appends($request->except('accounts_page'));
                $accounts->getCollection()->transform(function ($row) {
                    $balance = (float) $row->current_balance;
                    $createdAt = $row->join_date ?? $row->updated_at ?? now();
                    $derivedAccountNumber = 'SAV-BSS-C15-' . str_pad((string) $row->member_id, 4, '0', STR_PAD_LEFT);
                    return (object) [
                        'member_id' => $row->member_id,
                        'account_number' => $derivedAccountNumber,
                        'account_name' => 'Member Savings',
                        'current_balance' => $balance,
                        'available_balance' => $balance,
                        'overdraft_used' => 0,
                        'overdraft_limit' => 0,
                        'accrued_interest' => 0,
                        'last_interest_calculation' => null,
                        'status' => $balance > 0 ? 'active' : 'inactive',
                        'opening_date' => $row->join_date,
                        'maturity_date' => null,
                        'updated_at' => $row->updated_at,
                        'created_at' => $createdAt,
                        'is_joint' => 0,
                        'member' => (object) [
                            'full_name' => $row->full_name,
                            'member_number' => $row->member_account_number ?? $row->member_number,
                            'profile_picture_url' => $this->resolveMemberPictureUrl($row->profile_picture ?? null),
                        ],
                    ];
                });
            }
        } else {
            $accountStatuses = collect(['active', 'inactive']);

            $derivedBalancesSub = DB::table('transactions')
                ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
                ->join('transaction_statuses as ts', 'transactions.status_id', '=', 'ts.id')
                ->where('ts.name', 'completed')
                ->where('tt.affects_savings', 1)
                ->selectRaw("transactions.member_id, COALESCE(SUM(CASE WHEN tt.impact = 'credit' THEN {$amountSql} ELSE -{$amountSql} END), 0) as balance")
                ->groupBy('transactions.member_id');
            $excludeFundraising($derivedBalancesSub);

            $accountsQuery = DB::table('members')
                ->leftJoinSub($derivedBalancesSub, 'balances', 'members.id', '=', 'balances.member_id')
                ->select(
                    'members.id as member_id',
                    'members.full_name',
                    'members.member_account_number',
                    DB::raw('COALESCE(members.member_account_number, members.member_number) as member_number'),
                    'members.profile_picture',
                    'members.join_date',
                    'members.updated_at',
                    DB::raw('COALESCE(balances.balance, 0) as current_balance')
                );

            if ($request->filled('accounts_search')) {
                $term = '%' . $request->input('accounts_search') . '%';
                $accountsQuery->where(function ($q) use ($term) {
                    $q->where('members.full_name', 'like', $term)
                        ->orWhere('members.member_account_number', 'like', $term)
                        ->orWhere('members.member_number', 'like', $term);
                });
            }

            if ($request->filled('accounts_status')) {
                $status = $request->input('accounts_status');
                if ($status === 'active') {
                    $accountsQuery->whereRaw('COALESCE(balances.balance, 0) > 0');
                } elseif ($status === 'inactive') {
                    $accountsQuery->whereRaw('COALESCE(balances.balance, 0) <= 0');
                }
            }

            if ($request->filled('accounts_min')) {
                $accountsQuery->whereRaw('COALESCE(balances.balance, 0) >= ?', [$request->input('accounts_min')]);
            }
            if ($request->filled('accounts_max')) {
                $accountsQuery->whereRaw('COALESCE(balances.balance, 0) <= ?', [$request->input('accounts_max')]);
            }
            if ($request->filled('accounts_opened_from')) {
                $accountsQuery->whereDate('members.join_date', '>=', $request->input('accounts_opened_from'));
            }
            if ($request->filled('accounts_opened_to')) {
                $accountsQuery->whereDate('members.join_date', '<=', $request->input('accounts_opened_to'));
            }
            if ($request->filled('accounts_joint')) {
                if ($request->input('accounts_joint') === 'yes') {
                    $accountsQuery->whereRaw('1 = 0');
                }
            }
            if ($request->filled('accounts_maturity_from') || $request->filled('accounts_maturity_to')) {
                $accountsQuery->whereRaw('1 = 0');
            }

            $accountsQuery->orderByDesc('current_balance');

            $accounts = $accountsQuery
                ->paginate(15, ['*'], 'accounts_page')
                ->appends($request->except('accounts_page'));
            $accounts->getCollection()->transform(function ($row) {
                $balance = (float) $row->current_balance;
                $createdAt = $row->join_date ?? $row->updated_at ?? now();
                $derivedAccountNumber = 'SAV-BSS-C15-' . str_pad((string) $row->member_id, 4, '0', STR_PAD_LEFT);
                return (object) [
                    'member_id' => $row->member_id,
                    'account_number' => $derivedAccountNumber,
                    'account_name' => 'Member Savings',
                    'current_balance' => $balance,
                    'available_balance' => $balance,
                    'overdraft_used' => 0,
                    'overdraft_limit' => 0,
                    'accrued_interest' => 0,
                    'last_interest_calculation' => null,
                    'status' => $balance > 0 ? 'active' : 'inactive',
                    'opening_date' => $row->join_date,
                    'maturity_date' => null,
                    'updated_at' => $row->updated_at,
                    'created_at' => $createdAt,
                    'is_joint' => 0,
                    'member' => (object) [
                        'full_name' => $row->full_name,
                        'member_number' => $row->member_account_number ?? $row->member_number,
                        'profile_picture_url' => $this->resolveMemberPictureUrl($row->profile_picture ?? null),
                    ],
                ];
            });
        }

        $movementStatuses = $remember('movement_statuses', $lookupTtl, function () {
            return DB::table('transaction_statuses')->orderBy('name')->pluck('name');
        });
        $movementTypes = $remember('movement_types', $lookupTtl, function () {
            return DB::table('transaction_types')
                ->whereIn('name', ['deposit', 'withdrawal', 'transfer', 'loan_disbursement'])
                ->orderBy('name')
                ->pluck('name');
        });
        $movementCategories = $remember('movement_categories', $lookupTtl, function () use ($categoryNames) {
            return DB::table('transaction_categories')
                ->whereIn('name', $categoryNames)
                ->orderBy('display_name')
                ->get(['name', 'display_name']);
        });

        $movementsQuery = Transaction::query()
            ->with(['member', 'transactionType', 'transactionCategory', 'statusRelation'])
            ->when($statusFilterId, fn ($q) => $q->where('transactions.status_id', $statusFilterId))
            ->whereHas('transactionCategory', fn ($q) => $q->whereIn('name', $categoryNames))
            ->whereNotExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('fundraising_contributions as fc')
                    ->whereColumn('fc.transaction_id', 'transactions.id');
            })
            ->whereRaw("JSON_EXTRACT(transactions.metadata, '$.campaign_id') IS NULL")
            ->when($request->filled('movement_search'), function ($q) use ($request) {
                $term = '%' . $request->input('movement_search') . '%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('transaction_number', 'like', $term)
                        ->orWhere('reference_number', 'like', $term)
                        ->orWhere('receipt_number', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhereHas('member', fn ($m) => $m->where('full_name', 'like', $term)
                            ->orWhere('member_account_number', 'like', $term)
                            ->orWhere('member_number', 'like', $term));
                });
            })
            ->when($request->filled('movement_type'), fn ($q) => $q->whereHas('transactionType', fn ($t) => $t->where('name', $request->input('movement_type'))))
            ->when($request->filled('movement_category'), fn ($q) => $q->whereHas('transactionCategory', fn ($c) => $c->where('name', $request->input('movement_category'))))
            ->when($request->filled('movement_date_from'), fn ($q) => $q->whereDate('transaction_date', '>=', $request->input('movement_date_from')))
            ->when($request->filled('movement_date_to'), fn ($q) => $q->whereDate('transaction_date', '<=', $request->input('movement_date_to')))
            ->when($request->filled('movement_amount_min'), fn ($q) => $q->whereRaw("{$amountSql} >= ?", [$request->input('movement_amount_min')]))
            ->when($request->filled('movement_amount_max'), fn ($q) => $q->whereRaw("{$amountSql} <= ?", [$request->input('movement_amount_max')]))
            ->when($request->filled('movement_status'), fn ($q) => $q->whereHas('statusRelation', fn ($s) => $s->where('name', $request->input('movement_status'))));

        $movementsSummary = (clone $movementsQuery)
            ->reorder()
            ->selectRaw("COALESCE(COUNT(*), 0) as total_count")
            ->selectRaw("COALESCE(SUM({$amountSql}), 0) as total_amount")
            ->selectRaw("COALESCE(AVG({$amountSql}), 0) as avg_amount")
            ->selectRaw("COALESCE(MAX({$amountSql}), 0) as max_amount")
            ->first();

        $movementCategoryStats = (clone $movementsQuery)
            ->reorder()
            ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
            ->select('tc.name', DB::raw("COALESCE(SUM({$amountSql}), 0) as total"), DB::raw('COUNT(*) as count'))
            ->groupBy('tc.name')
            ->get()
            ->keyBy('name');

        $movementChartLabels = ['Savings Deposits (incl. Loans)', 'Savings Withdrawals', 'Transfers In', 'Transfers Out'];
        $movementChartTotals = [
            (float) ($movementCategoryStats->get('savings_deposit')->total ?? 0) + (float) ($movementCategoryStats->get('loan_disbursement')->total ?? 0),
            (float) ($movementCategoryStats->get('savings_withdrawal')->total ?? 0),
            (float) ($movementCategoryStats->get('transfer_in')->total ?? 0),
            (float) ($movementCategoryStats->get('transfer_out')->total ?? 0),
        ];

        $movements = $movementsQuery
            ->latest()
            ->paginate(20, ['*'], 'movements_page')
            ->appends($request->except('movements_page'));

        $transactionEntityId = $remember('entity_type_transaction', $lookupTtl, function () {
            return DB::table('entity_types')->where('name', 'transaction')->value('id');
        });
        $auditLogs = DB::table('audit_logs')
            ->leftJoin('audit_action_types as aat', 'aat.id', '=', 'audit_logs.action_type_id')
            ->leftJoin('users', 'users.id', '=', 'audit_logs.user_id')
            ->leftJoin('members', 'members.id', '=', 'audit_logs.member_id')
            ->leftJoin('transactions', 'transactions.id', '=', 'audit_logs.entity_id')
            ->leftJoin('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
            ->when($transactionEntityId, fn ($q) => $q->where('audit_logs.entity_type_id', $transactionEntityId))
            ->whereIn('tc.name', $categoryNames)
            ->whereNotExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('fundraising_contributions as fc')
                    ->whereColumn('fc.transaction_id', 'transactions.id');
            })
            ->whereRaw("JSON_EXTRACT(transactions.metadata, '$.campaign_id') IS NULL")
            ->when($request->filled('audit_search'), function ($q) use ($request) {
                $term = '%' . $request->input('audit_search') . '%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('audit_logs.description', 'like', $term)
                        ->orWhere('audit_logs.entity_identifier', 'like', $term)
                        ->orWhere('users.username', 'like', $term)
                        ->orWhere('members.full_name', 'like', $term);
                });
            })
            ->when($request->filled('audit_action'), fn ($q) => $q->where('aat.name', $request->input('audit_action')))
            ->when($request->filled('audit_category'), fn ($q) => $q->where('tc.name', $request->input('audit_category')))
            ->when($request->filled('audit_date_from'), fn ($q) => $q->whereDate('audit_logs.created_at', '>=', $request->input('audit_date_from')))
            ->when($request->filled('audit_date_to'), fn ($q) => $q->whereDate('audit_logs.created_at', '<=', $request->input('audit_date_to')))
            ->orderByDesc('audit_logs.created_at')
            ->limit(20)
            ->get([
                'audit_logs.id',
                'audit_logs.description',
                'audit_logs.entity_identifier',
                'audit_logs.created_at',
                'aat.name as action',
                'users.username as user_name',
                'members.full_name as member_name',
                'members.profile_picture as member_picture',
                'tc.display_name as category_name',
            ]);

        $auditLogs = $auditLogs->map(function ($log) {
            $log->member_picture_url = $this->resolveMemberPictureUrl($log->member_picture ?? null);
            return $log;
        });

        if ($auditLogs->isEmpty()) {
            $auditLogs = (clone $movementsQuery)
                ->reorder()
                ->latest()
                ->limit(20)
                ->get()
                ->map(function ($movement) {
                    $memberPicture = $movement->member?->profile_picture_url ?? asset('images/default-avatar.svg');
                    return (object) [
                        'id' => $movement->id,
                        'description' => $movement->description ?? 'Savings movement',
                        'entity_identifier' => $movement->transaction_number ?? $movement->reference_number,
                        'created_at' => $movement->transaction_date ?? $movement->created_at,
                        'action' => $movement->transactionType->name ?? 'movement',
                        'user_name' => $movement->processed_by ? 'User #' . $movement->processed_by : 'System',
                        'member_name' => $movement->member->full_name ?? 'N/A',
                        'member_picture_url' => $memberPicture,
                        'category_name' => $movement->transactionCategory->display_name ?? $movement->transactionCategory->name ?? 'Savings',
                    ];
                });
        }

        return view('cashier.savings.index', compact(
            'savingsDeposits',
            'savingsWithdrawals',
            'savingsNet',
            'totalSavingsInterestProfit',
            'totalLoanInterestProfit',
            'totalInterestProfit',
            'profitMargin',
            'totalSavingsBalance',
            'totalSavingsBalanceAccounts',
            'totalSavingsBalanceTransactions',
            'totalAvailableBalance',
            'totalOverdraftLimit',
            'totalOverdraftUsed',
            'totalAccounts',
            'avgBalance',
            'activeAccounts',
            'frozenAccounts',
            'closedAccounts',
            'jointAccounts',
            'maturedAccounts',
            'statusBreakdown',
            'accountStatuses',
            'last30dSavingsNet',
            'monthlyLabels',
            'monthlyDeposits',
            'monthlyWithdrawals',
            'monthlyNet',
            'topMembers',
            'goalProgress',
            'totalMembersWithSavings',
            'reconTolerance',
            'mismatchCount',
            'mismatches',
            'lowBalanceThreshold',
            'largeWithdrawalThreshold',
            'largeWithdrawalDays',
            'lowBalanceAccounts',
            'largeWithdrawals',
            'lastSavingsDates',
            'accounts',
            'movements',
            'movementsSummary',
            'movementCategoryStats',
            'movementChartLabels',
            'movementChartTotals',
            'movementStatuses',
            'movementTypes',
            'movementCategories',
            'balanceSourceCounts',
            'reconciliationStats',
            'topTenShare',
            'liquidityRatio',
            'overdraftUtilization',
            'savingsVelocity',
            'auditLogs',
            'settingsInterestRate',
            'useComputedSavingsInterest'
        ));
    }

    public function updateInterestRate(Request $request)
    {
        $validated = $request->validate([
            'interest_rate' => 'required|numeric|min:0|max:100',
        ]);

        Setting::set('interest_rate', $validated['interest_rate']);

        return redirect()
            ->route('cashier.savings.index')
            ->with('success', 'Savings interest rate updated.');
    }

    private function resolveMemberPictureUrl(?string $path): string
    {
        $member = new Member(['profile_picture' => $path]);
        return $member->profile_picture_url ?? asset('images/default-avatar.svg');
    }
}
