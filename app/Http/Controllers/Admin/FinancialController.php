<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\MemberDividend;
use App\Models\Share;
use App\Models\Transaction;
use App\Models\Fundraising;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
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

        $totals = Transaction::query()
            ->when($statusFilterId, fn ($q) => $q->where('transactions.status_id', $statusFilterId))
            ->join('transaction_types', 'transactions.transaction_type_id', '=', 'transaction_types.id')
            ->selectRaw("COALESCE(SUM(CASE WHEN transaction_types.name = 'deposit' THEN {$amountSql} ELSE 0 END), 0) as total_deposits")
            ->selectRaw("COALESCE(SUM(CASE WHEN transaction_types.name = 'withdrawal' THEN {$amountSql} ELSE 0 END), 0) as total_withdrawals")
            ->selectRaw("COALESCE(SUM(CASE WHEN transaction_types.name = 'transfer' THEN {$amountSql} ELSE 0 END), 0) as total_transfers")
            ->selectRaw("COALESCE(SUM(CASE WHEN transaction_types.name IN ('loan_repayment', 'loan_payment', 'repayment') THEN {$amountSql} ELSE 0 END), 0) as total_loan_payments")
            ->first();

        $deposits = (float) ($totals->total_deposits ?? 0);
        $withdrawals = (float) ($totals->total_withdrawals ?? 0);
        $transfers = (float) ($totals->total_transfers ?? 0);
        $loanPayments = (float) ($totals->total_loan_payments ?? 0);

        $totalRevenue = $deposits + $loanPayments;
        $totalExpenses = $withdrawals + $transfers;
        $netProfit = $totalRevenue - $totalExpenses;

        $totalAssets = (float) DB::table('savings_accounts')->sum('current_balance');
        if ($totalAssets == 0.0) {
            $totalAssets = (float) DB::table('transactions')
                ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
                ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
                ->when($statusFilterId, fn ($q) => $q->where('transactions.status_id', $statusFilterId))
                ->whereIn('tc.name', ['savings_deposit', 'savings_withdrawal', 'transfer_out', 'transfer_in', 'fundraising_transfer', 'loan_disbursement'])
                ->selectRaw("COALESCE(SUM(CASE WHEN tt.impact = 'debit' THEN -{$amountSql} ELSE {$amountSql} END), 0) as balance")
                ->value('balance');
        }
        $totalLiabilities = (float) Loan::query()->sum('balance_due');
        $totalEquity = $totalAssets - $totalLiabilities;

        $totalShares = (float) Share::query()->sum(DB::raw('shares_count * current_value')) ?: 0;
        $totalDividendsPaid = (float) MemberDividend::query()->where('status', 'paid')->sum('net_amount') ?: 0;
        $totalDividendsPending = (float) MemberDividend::query()->where('status', 'pending')->sum('net_amount') ?: 0;
        $fundraisingTotal = (float) DB::table('fundraising_contributions')->sum('amount');

        $activeLoans = Loan::query()->status('approved')->count();
        $totalLoanAmount = (float) Loan::query()->status('approved')->sum('principal_amount') ?: 0;
        $totalInterestEarned = (float) Loan::query()->status('approved')->sum('total_interest') ?: 0;
        $avgInterestRate = (float) Loan::query()->status('approved')->avg('interest_rate') ?: 0;

        $monthlyData = Transaction::query()
            ->whereRaw('COALESCE(transactions.transaction_date, transactions.created_at) >= ?', [now()->subMonths(12)])
            ->when($statusFilterId, fn ($q) => $q->where('transactions.status_id', $statusFilterId))
            ->join('transaction_types', 'transactions.transaction_type_id', '=', 'transaction_types.id')
            ->selectRaw('MONTH(COALESCE(transactions.transaction_date, transactions.created_at)) as month, transaction_types.name as type_name, SUM(transactions.amount) as total')
            ->groupBy('month', 'type_name')
            ->get();

        $monthlyRevenue = array_fill(1, 12, 0);
        $monthlyExpenses = array_fill(1, 12, 0);

        foreach ($monthlyData as $data) {
            if (in_array($data->type_name, ['deposit', 'loan_repayment', 'loan_payment'], true)) {
                $monthlyRevenue[$data->month] += $data->total;
            } elseif (in_array($data->type_name, ['withdrawal', 'transfer'], true)) {
                $monthlyExpenses[$data->month] += $data->total;
            }
        }

        $monthlyNet = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyNet[] = ($monthlyRevenue[$i] ?? 0) - ($monthlyExpenses[$i] ?? 0);
        }

        $categoryTotals = DB::table('transactions')
            ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
            ->when($statusFilterId, fn ($q) => $q->where('transactions.status_id', $statusFilterId))
            ->selectRaw('tc.display_name as label, SUM(transactions.amount) as total')
            ->groupBy('tc.display_name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $typeTotals = DB::table('transactions')
            ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
            ->when($statusFilterId, fn ($q) => $q->where('transactions.status_id', $statusFilterId))
            ->selectRaw('tt.display_name as label, SUM(transactions.amount) as total')
            ->groupBy('tt.display_name')
            ->orderByDesc('total')
            ->get();

        $netPosition = ($totalAssets + $totalShares) - ($totalLiabilities + $totalDividendsPending);
        $last30dStart = now()->subDays(30);
        $last30dRevenue = Transaction::query()
            ->when($statusFilterId, fn ($q) => $q->where('transactions.status_id', $statusFilterId))
            ->whereRaw('COALESCE(transactions.transaction_date, transactions.created_at) >= ?', [$last30dStart])
            ->whereHas('transactionType', fn ($q) => $q->whereIn('name', ['deposit', 'loan_repayment', 'loan_payment']))
            ->sum('amount');
        $last30dExpenses = Transaction::query()
            ->when($statusFilterId, fn ($q) => $q->where('transactions.status_id', $statusFilterId))
            ->whereRaw('COALESCE(transactions.transaction_date, transactions.created_at) >= ?', [$last30dStart])
            ->whereHas('transactionType', fn ($q) => $q->whereIn('name', ['withdrawal', 'transfer']))
            ->sum('amount');
        $last30dNet = (float) $last30dRevenue - (float) $last30dExpenses;

        $transactions = Transaction::query()
            ->with(['member', 'transactionType', 'transactionCategory', 'statusRelation'])
            ->when($request->filled('transactions_search'), function ($q) use ($request) {
                $term = '%' . $request->input('transactions_search') . '%';
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
            ->when($request->filled('transactions_type'), fn ($q) => $q->whereHas('transactionType', fn ($t) => $t->where('name', $request->input('transactions_type'))))
            ->when($request->filled('transactions_status'), fn ($q) => $q->whereHas('statusRelation', fn ($s) => $s->where('name', $request->input('transactions_status'))))
            ->when($request->filled('transactions_date_from'), fn ($q) => $q->whereDate('transaction_date', '>=', $request->input('transactions_date_from')))
            ->when($request->filled('transactions_date_to'), fn ($q) => $q->whereDate('transaction_date', '<=', $request->input('transactions_date_to')))
            ->when($request->filled('transactions_amount_min'), fn ($q) => $q->where('amount', '>=', $request->input('transactions_amount_min')))
            ->when($request->filled('transactions_amount_max'), fn ($q) => $q->where('amount', '<=', $request->input('transactions_amount_max')))
            ->latest()
            ->paginate($request->per_page ?? 10)
            ->appends($request->except('page'));

        $transactionsFundraisingTotal = Transaction::query()
            ->whereHas('transactionCategory', function ($q) {
                $q->where('name', 'like', 'fundraising_%');
            })
            ->sum('amount');

        $fundraisingCampaignTotals = Fundraising::query()
            ->withSum('contributions', 'amount')
            ->orderBy('title')
            ->get(['id', 'title']);

        $dividends = MemberDividend::query()
            ->with(['member', 'dividend'])
            ->when($request->filled('dividends_search'), function ($q) use ($request) {
                $term = '%' . $request->input('dividends_search') . '%';
                $q->whereHas('member', fn ($m) => $m->where('full_name', 'like', $term)
                    ->orWhere('member_account_number', 'like', $term)
                    ->orWhere('member_number', 'like', $term))
                  ->orWhere('net_amount', 'like', $term);
            })
            ->when($request->filled('dividends_status'), fn ($q) => $q->where('status', $request->input('dividends_status')))
            ->when($request->filled('dividends_date_from'), fn ($q) => $q->whereRaw('DATE(COALESCE(paid_at, created_at)) >= ?', [$request->input('dividends_date_from')]))
            ->when($request->filled('dividends_date_to'), fn ($q) => $q->whereRaw('DATE(COALESCE(paid_at, created_at)) <= ?', [$request->input('dividends_date_to')]))
            ->when($request->filled('dividends_amount_min'), fn ($q) => $q->where('net_amount', '>=', $request->input('dividends_amount_min')))
            ->when($request->filled('dividends_amount_max'), fn ($q) => $q->where('net_amount', '<=', $request->input('dividends_amount_max')))
            ->latest()
            ->paginate($request->per_page ?? 10)
            ->appends($request->except('page'));

        $shares = Share::query()
            ->with('member')
            ->when($request->filled('shares_search'), function ($q) use ($request) {
                $term = '%' . $request->input('shares_search') . '%';
                $q->where('certificate_number', 'like', $term)
                  ->orWhere('shares_count', 'like', $term)
                  ->orWhereHas('member', fn ($m) => $m->where('full_name', 'like', $term)
                      ->orWhere('member_account_number', 'like', $term)
                      ->orWhere('member_number', 'like', $term));
            })
            ->when($request->filled('shares_status'), function ($q) use ($request) {
                $statusId = DB::table('share_statuses')->where('name', $request->input('shares_status'))->value('id');
                if ($statusId) {
                    $q->where('status_id', $statusId);
                }
            })
            ->when($request->filled('shares_date_from'), fn ($q) => $q->whereDate('purchase_date', '>=', $request->input('shares_date_from')))
            ->when($request->filled('shares_date_to'), fn ($q) => $q->whereDate('purchase_date', '<=', $request->input('shares_date_to')))
            ->when($request->filled('shares_value_min'), fn ($q) => $q->where('current_value', '>=', $request->input('shares_value_min')))
            ->when($request->filled('shares_value_max'), fn ($q) => $q->where('current_value', '<=', $request->input('shares_value_max')))
            ->latest()
            ->paginate($request->per_page ?? 10)
            ->appends($request->except('page'));

        $loans = Loan::query()
            ->with('member')
            ->when($request->filled('loans_search'), function ($q) use ($request) {
                $term = '%' . $request->input('loans_search') . '%';
                $q->where('loan_number', 'like', $term)
                    ->orWhere('loan_number', 'like', $term)
                    ->orWhereHas('member', fn ($m) => $m->where('full_name', 'like', $term)
                        ->orWhere('member_account_number', 'like', $term)
                        ->orWhere('member_number', 'like', $term));
            })
            ->when($request->filled('loans_status'), fn ($q) => $q->status($request->input('loans_status')))
            ->when($request->filled('loans_date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('loans_date_from')))
            ->when($request->filled('loans_date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('loans_date_to')))
            ->when($request->filled('loans_amount_min'), fn ($q) => $q->where('principal_amount', '>=', $request->input('loans_amount_min')))
            ->when($request->filled('loans_amount_max'), fn ($q) => $q->where('principal_amount', '<=', $request->input('loans_amount_max')))
            ->latest()
            ->paginate($request->per_page ?? 10)
            ->appends($request->except('page'));

        $savingsTransactions = $this->applyTransactionFilters(
            Transaction::query(),
            $request,
            'savings',
            true
        )
            ->whereHas('transactionCategory', function ($qc) {
                $qc->whereIn('name', ['savings_deposit', 'savings_withdrawal', 'transfer_in', 'transfer_out', 'fundraising_transfer', 'loan_disbursement']);
            })
            ->with(['member', 'transactionType', 'transactionCategory', 'statusRelation'])
            ->latest()
            ->get();

        $expenseTransactions = $this->applyTransactionFilters(
            Transaction::query(),
            $request,
            'expenses',
            true
        )
            ->whereHas('transactionType', fn ($qt) => $qt->where('name', 'withdrawal'))
            ->with(['member', 'transactionType', 'transactionCategory', 'statusRelation'])
            ->latest()
            ->get();

        $revenueTransactions = $this->applyTransactionFilters(
            Transaction::query(),
            $request,
            'revenue',
            true
        )
            ->whereHas('transactionType', fn ($qt) => $qt->whereIn('name', ['deposit', 'loan_repayment', 'loan_payment', 'repayment']))
            ->with(['member', 'transactionType', 'transactionCategory', 'statusRelation'])
            ->latest()
            ->get();

        $assetSavingsAccounts = DB::table('savings_accounts')
            ->join('members', 'members.id', '=', 'savings_accounts.member_id')
            ->select(
                'savings_accounts.account_number',
                'savings_accounts.account_name',
                'savings_accounts.current_balance',
                'savings_accounts.available_balance',
                'savings_accounts.status',
                'savings_accounts.opening_date',
                'savings_accounts.updated_at',
                'members.full_name as member_name',
                DB::raw('COALESCE(members.member_account_number, members.member_number) as member_code')
            )
            ->when($request->filled('assets_savings_search'), function ($q) use ($request) {
                $term = '%' . $request->input('assets_savings_search') . '%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('savings_accounts.account_number', 'like', $term)
                        ->orWhere('savings_accounts.account_name', 'like', $term)
                        ->orWhere('members.full_name', 'like', $term)
                        ->orWhere('members.member_account_number', 'like', $term)
                        ->orWhere('members.member_number', 'like', $term);
                });
            })
            ->when($request->filled('assets_savings_status'), fn ($q) => $q->where('savings_accounts.status', $request->input('assets_savings_status')))
            ->when($request->filled('assets_savings_min'), fn ($q) => $q->where('savings_accounts.current_balance', '>=', $request->input('assets_savings_min')))
            ->when($request->filled('assets_savings_max'), fn ($q) => $q->where('savings_accounts.current_balance', '<=', $request->input('assets_savings_max')))
            ->orderByDesc('savings_accounts.updated_at')
            ->get();

        $assetShares = Share::query()
            ->with('member')
            ->when($request->filled('assets_shares_search'), function ($q) use ($request) {
                $term = '%' . $request->input('assets_shares_search') . '%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('certificate_number', 'like', $term)
                        ->orWhereHas('member', fn ($m) => $m->where('full_name', 'like', $term)
                            ->orWhere('member_account_number', 'like', $term)
                            ->orWhere('member_number', 'like', $term));
                });
            })
            ->when($request->filled('assets_shares_min'), fn ($q) => $q->where('current_value', '>=', $request->input('assets_shares_min')))
            ->when($request->filled('assets_shares_max'), fn ($q) => $q->where('current_value', '<=', $request->input('assets_shares_max')))
            ->latest()
            ->get();

        $liabilityLoans = Loan::query()
            ->with('member')
            ->when($request->filled('liabilities_loans_search'), function ($q) use ($request) {
                $term = '%' . $request->input('liabilities_loans_search') . '%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('loan_number', 'like', $term)
                        ->orWhere('loan_number', 'like', $term)
                        ->orWhereHas('member', fn ($m) => $m->where('full_name', 'like', $term)
                            ->orWhere('member_account_number', 'like', $term)
                            ->orWhere('member_number', 'like', $term));
                });
            })
            ->when($request->filled('liabilities_loans_status'), fn ($q) => $q->status($request->input('liabilities_loans_status')))
            ->when($request->filled('liabilities_loans_min'), fn ($q) => $q->where('balance_due', '>=', $request->input('liabilities_loans_min')))
            ->when($request->filled('liabilities_loans_max'), fn ($q) => $q->where('balance_due', '<=', $request->input('liabilities_loans_max')))
            ->latest()
            ->get();

        $liabilityDividends = MemberDividend::query()
            ->with(['member', 'dividend'])
            ->where('status', 'pending')
            ->when($request->filled('liabilities_dividends_search'), function ($q) use ($request) {
                $term = '%' . $request->input('liabilities_dividends_search') . '%';
                $q->whereHas('member', fn ($m) => $m->where('full_name', 'like', $term)
                    ->orWhere('member_account_number', 'like', $term)
                    ->orWhere('member_number', 'like', $term));
            })
            ->when($request->filled('liabilities_dividends_year'), function ($q) use ($request) {
                $q->whereHas('dividend', fn ($d) => $d->where('year', $request->input('liabilities_dividends_year')));
            })
            ->when($request->filled('liabilities_dividends_quarter'), function ($q) use ($request) {
                $q->whereHas('dividend', fn ($d) => $d->where('quarter', $request->input('liabilities_dividends_quarter')));
            })
            ->latest()
            ->get();

        $quarterlyDividends = MemberDividend::query()
            ->selectRaw('YEAR(COALESCE(paid_at, created_at)) as year, QUARTER(COALESCE(paid_at, created_at)) as quarter, SUM(net_amount) as total')
            ->whereRaw('COALESCE(paid_at, created_at) >= ?', [now()->subYear()])
            ->groupBy('year', 'quarter')
            ->orderByDesc('year')
            ->orderByDesc('quarter')
            ->get();

        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
        $roi = $totalAssets > 0 ? ($netProfit / $totalAssets) * 100 : 0;
        $debtToEquity = $totalEquity > 0 ? $totalLiabilities / $totalEquity : 0;
        $currentRatio = $totalLiabilities > 0 ? $totalAssets / $totalLiabilities : 0;

        $transactionsCount = Transaction::query()->count();
        $dividendsCount = MemberDividend::query()->count();
        $sharesCount = Share::query()->count();
        $loansCount = Loan::query()->count();

        return view('admin.financial.index', compact(
            'totalRevenue', 'totalExpenses', 'netProfit', 'totalAssets', 'totalLiabilities',
            'totalEquity', 'totalShares', 'totalDividendsPaid', 'totalDividendsPending',
            'activeLoans', 'totalLoanAmount', 'totalInterestEarned', 'avgInterestRate',
            'monthlyRevenue', 'monthlyExpenses', 'monthlyNet', 'quarterlyDividends',
            'profitMargin', 'roi', 'debtToEquity', 'currentRatio',
            'deposits', 'withdrawals', 'transfers', 'loanPayments', 'fundraisingTotal',
            'transactions', 'dividends', 'shares', 'loans',
            'transactionsCount', 'dividendsCount', 'sharesCount', 'loansCount',
            'savingsTransactions', 'expenseTransactions', 'revenueTransactions',
            'assetSavingsAccounts', 'assetShares', 'liabilityLoans', 'liabilityDividends',
            'transactionsFundraisingTotal', 'fundraisingCampaignTotals',
            'categoryTotals', 'typeTotals', 'netPosition', 'last30dNet'
        ));
    }

    private function applyTransactionFilters($query, Request $request, string $prefix, bool $includeMember = false)
    {
        $search = $request->input("{$prefix}_search");
        if (!empty($search)) {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term, $includeMember) {
                $q->where('transaction_number', 'like', $term)
                    ->orWhere('reference_number', 'like', $term)
                    ->orWhere('receipt_number', 'like', $term)
                    ->orWhere('description', 'like', $term);
                if ($includeMember) {
                    $q->orWhereHas('member', function ($m) use ($term) {
                        $m->where('full_name', 'like', $term)
                          ->orWhere('member_account_number', 'like', $term)
                          ->orWhere('member_number', 'like', $term);
                    });
                }
            });
        }

        if ($request->filled("{$prefix}_date_from")) {
            $query->whereDate('transaction_date', '>=', $request->input("{$prefix}_date_from"));
        }
        if ($request->filled("{$prefix}_date_to")) {
            $query->whereDate('transaction_date', '<=', $request->input("{$prefix}_date_to"));
        }
        if ($request->filled("{$prefix}_amount_min")) {
            $query->where('amount', '>=', $request->input("{$prefix}_amount_min"));
        }
        if ($request->filled("{$prefix}_amount_max")) {
            $query->where('amount', '<=', $request->input("{$prefix}_amount_max"));
        }
        if ($request->filled("{$prefix}_status")) {
            $query->whereHas('statusRelation', fn ($q) => $q->where('name', $request->input("{$prefix}_status")));
        }

        return $query;
    }
}
