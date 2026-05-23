<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Share;
use App\Models\MemberDividend;
use App\Models\Project;
use App\Models\Fundraising;
use App\Services\Financial\MemberFinancialSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();
        
        if (!$member) {
            return redirect()->back()->with('error', 'Member profile not found');
        }

        $financialSummary = app(MemberFinancialSyncService::class)->getMemberFinancialSummary($member);

        $deposits = $financialSummary['total_deposits'] ?? 0;
        $withdrawals = $financialSummary['total_withdrawals'] ?? 0;
        $loanPayments = $financialSummary['total_loan_payments'] ?? 0;
        
        $totalRevenue = $deposits + $loanPayments;
        $totalExpenses = $withdrawals;
        $netProfit = $totalRevenue - $totalExpenses;
        
        $totalAssets = $financialSummary['available_balance'] ?? 0;
        if ($totalAssets == 0.0) {
            $amountSql = 'COALESCE(NULLIF(transactions.net_amount, 0), transactions.amount, 0)';
            $totalAssets = (float) DB::table('transactions')
                ->join('transaction_categories as tc', 'transactions.category_id', '=', 'tc.id')
                ->join('transaction_types as tt', 'transactions.transaction_type_id', '=', 'tt.id')
                ->where('transactions.member_id', $member->id)
                ->whereIn('tc.name', ['savings_deposit', 'savings_withdrawal', 'transfer_out', 'transfer_in', 'fundraising_transfer', 'loan_disbursement'])
                ->selectRaw("COALESCE(SUM(CASE WHEN tt.impact = 'debit' THEN -{$amountSql} ELSE {$amountSql} END), 0) as balance")
                ->value('balance');
        }
        $totalLiabilities = $financialSummary['loan_outstanding'] ?? 0;
        $totalEquity = $totalAssets - $totalLiabilities;
        
        $totalShares = Share::where('member_id', $member->id)->sum(DB::raw('shares_count * current_value')) ?: 0;
        $totalDividendsPaid = MemberDividend::where('member_id', $member->id)->where('status', 'paid')->sum('net_amount') ?: 0;
        $totalDividendsPending = MemberDividend::where('member_id', $member->id)->where('status', 'pending')->sum('net_amount') ?: 0;
        
        $activeLoans = Loan::where('member_id', $member->id)->status('approved')->count();
        $totalLoanAmount = Loan::where('member_id', $member->id)->status('approved')->sum('principal_amount') ?: 0;
        $totalInterestEarned = Loan::where('member_id', $member->id)->status('approved')->sum('total_interest') ?: 0;
        $avgInterestRate = Loan::where('member_id', $member->id)->status('approved')->avg('interest_rate') ?: 0;
        
        $monthlyData = Transaction::where('member_id', $member->id)
            ->whereRaw('COALESCE(transactions.transaction_date, transactions.created_at) >= ?', [now()->subMonths(12)])
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
        
        // Transactions with search (tab-specific filters)
        $transactionsQuery = Transaction::where('member_id', $member->id);

        $transactionsSearch = $request->input('transactions_search');
        if (!empty($transactionsSearch)) {
            $transactionsQuery->where(function($q) use ($transactionsSearch) {
                $q->where('transaction_number', 'like', "%{$transactionsSearch}%")
                  ->orWhere('description', 'like', "%{$transactionsSearch}%")
                  ->orWhere('reference_number', 'like', "%{$transactionsSearch}%")
                  ->orWhere('receipt_number', 'like', "%{$transactionsSearch}%")
                  ->orWhere('amount', 'like', "%{$transactionsSearch}%")
                  ->orWhereHas('transactionType', fn ($qt) => $qt->where('name', 'like', "%{$transactionsSearch}%"));
            });
        }
        if ($request->filled('transactions_type')) $transactionsQuery->ofType($request->transactions_type);
        if ($request->filled('transactions_status')) $transactionsQuery->whereHas('statusRelation', fn ($q) => $q->where('name', $request->transactions_status));
        if ($request->filled('transactions_date_from')) $transactionsQuery->whereDate('transactions.created_at', '>=', $request->transactions_date_from);
        if ($request->filled('transactions_date_to')) $transactionsQuery->whereDate('transactions.created_at', '<=', $request->transactions_date_to);
        if ($request->filled('transactions_amount_min')) $transactionsQuery->where('amount', '>=', $request->transactions_amount_min);
        if ($request->filled('transactions_amount_max')) $transactionsQuery->where('amount', '<=', $request->transactions_amount_max);
        
        if ($request->transactions_sort == 'amount_high') $transactionsQuery->orderBy('amount', 'desc');
        elseif ($request->transactions_sort == 'amount_low') $transactionsQuery->orderBy('amount', 'asc');
        elseif ($request->transactions_sort == 'oldest') $transactionsQuery->oldest();
        else $transactionsQuery->latest();
        
        $transactions = $transactionsQuery
            ->with(['transactionType', 'transactionCategory', 'statusRelation'])
            ->paginate($request->per_page ?? 10)
            ->appends($request->except('page'));

        $transactionsFundraisingTotal = Transaction::query()
            ->where('member_id', $member->id)
            ->whereHas('transactionCategory', function ($q) {
                $q->where('name', 'like', 'fundraising_%');
            })
            ->sum('amount');

        $fundraisingCampaignTotals = Fundraising::query()
            ->withSum('contributions', 'amount')
            ->orderBy('title')
            ->get(['id', 'title']);
        
        // Dividends with search (tab-specific filters)
        $dividendsQuery = MemberDividend::where('member_id', $member->id);

        $dividendsSearch = $request->input('dividends_search');
        if (!empty($dividendsSearch)) {
            $dividendsQuery->where(function($q) use ($dividendsSearch) {
                $q->where('net_amount', 'like', "%{$dividendsSearch}%")
                  ->orWhereHas('dividend', function ($dividendQuery) use ($dividendsSearch) {
                      $dividendQuery->where('year', 'like', "%{$dividendsSearch}%")
                          ->orWhere('quarter', 'like', "%{$dividendsSearch}%");
                  });
            });
        }
        if ($request->filled('dividends_status')) $dividendsQuery->where('status', $request->dividends_status);
        if ($request->filled('dividends_date_from')) $dividendsQuery->whereRaw('DATE(COALESCE(paid_at, created_at)) >= ?', [$request->dividends_date_from]);
        if ($request->filled('dividends_date_to')) $dividendsQuery->whereRaw('DATE(COALESCE(paid_at, created_at)) <= ?', [$request->dividends_date_to]);
        if ($request->filled('dividends_amount_min')) $dividendsQuery->where('net_amount', '>=', $request->dividends_amount_min);
        if ($request->filled('dividends_amount_max')) $dividendsQuery->where('net_amount', '<=', $request->dividends_amount_max);
        
        if ($request->dividends_sort == 'amount_high') $dividendsQuery->orderBy('net_amount', 'desc');
        elseif ($request->dividends_sort == 'amount_low') $dividendsQuery->orderBy('net_amount', 'asc');
        elseif ($request->dividends_sort == 'oldest') $dividendsQuery->oldest();
        else $dividendsQuery->latest();
        
        $dividends = $dividendsQuery
            ->with('dividend')
            ->paginate($request->per_page ?? 10)
            ->appends($request->except('page'));
        
        // Shares with search (tab-specific filters)
        $sharesQuery = Share::where('member_id', $member->id);

        $sharesSearch = $request->input('shares_search');
        if (!empty($sharesSearch)) {
            $sharesQuery->where(function($q) use ($sharesSearch) {
                $q->where('certificate_number', 'like', "%{$sharesSearch}%")
                  ->orWhere('shares_count', 'like', "%{$sharesSearch}%");
            });
        }
        if ($request->filled('shares_status')) {
            $statusId = DB::table('share_statuses')->where('name', $request->shares_status)->value('id');
            if ($statusId) {
                $sharesQuery->where('status_id', $statusId);
            }
        }
        if ($request->filled('shares_date_from')) $sharesQuery->whereDate('purchase_date', '>=', $request->shares_date_from);
        if ($request->filled('shares_date_to')) $sharesQuery->whereDate('purchase_date', '<=', $request->shares_date_to);
        if ($request->filled('shares_value_min')) $sharesQuery->where('current_value', '>=', $request->shares_value_min);
        if ($request->filled('shares_value_max')) $sharesQuery->where('current_value', '<=', $request->shares_value_max);
        
        if ($request->shares_sort == 'value_high') $sharesQuery->orderBy('current_value', 'desc');
        elseif ($request->shares_sort == 'value_low') $sharesQuery->orderBy('current_value', 'asc');
        elseif ($request->shares_sort == 'oldest') $sharesQuery->oldest();
        else $sharesQuery->latest();
        
        $shares = $sharesQuery
            ->paginate($request->per_page ?? 10)
            ->appends($request->except('page'));
        
        // Loans with search (tab-specific filters with backward-compatible loan_* params)
        $loansQuery = Loan::where('member_id', $member->id);

        $loanSearch = $request->input('loans_search', $request->input('loan_search'));

        if (!empty($loanSearch)) {
            $loansQuery->where(function($q) use ($loanSearch) {
                $q->where('loan_number', 'like', "%{$loanSearch}%")
                  ->orWhere('principal_amount', 'like', "%{$loanSearch}%");
            });
        }

        $loanStatus = $request->input('loans_status', $request->input('loan_status'));
        if (!empty($loanStatus)) {
            $loansQuery->status($loanStatus);
        }

        $loanDateFrom = $request->input('loans_date_from', $request->input('loan_date_from'));
        if (!empty($loanDateFrom)) {
            $loansQuery->whereDate('created_at', '>=', $loanDateFrom);
        }

        $loanDateTo = $request->input('loans_date_to', $request->input('loan_date_to'));
        if (!empty($loanDateTo)) {
            $loansQuery->whereDate('created_at', '<=', $loanDateTo);
        }

        $loanAmountMin = $request->input('loans_amount_min', $request->input('loan_amount_min'));
        if (!empty($loanAmountMin)) {
            $loansQuery->where('principal_amount', '>=', $loanAmountMin);
        }

        $loanAmountMax = $request->input('loans_amount_max');
        if (!empty($loanAmountMax)) {
            $loansQuery->where('principal_amount', '<=', $loanAmountMax);
        }

        $loanSort = $request->input('loans_sort', $request->input('loan_sort'));
        if ($loanSort == 'amount_high') $loansQuery->orderBy('principal_amount', 'desc');
        elseif ($loanSort == 'amount_low') $loansQuery->orderBy('principal_amount', 'asc');
        elseif ($loanSort == 'oldest') $loansQuery->oldest();
        else $loansQuery->latest();

        $loanPerPage = (int) ($request->input('loan_per_page') ?? ($request->input('tab') === 'loans' ? $request->input('per_page', 10) : 10));
        if (!in_array($loanPerPage, [10, 15, 20, 50, 100], true)) {
            $loanPerPage = 10;
        }

        $loans = $loansQuery
            ->paginate($loanPerPage, ['*'], 'loans_page')
            ->appends($request->except('loans_page'));

        $savingsTransactions = $this->applyTransactionFilters(
            Transaction::query(),
            $request,
            'savings'
        )
            ->where('member_id', $member->id)
            ->whereHas('transactionCategory', function ($qc) {
                $qc->whereIn('name', ['savings_deposit', 'savings_withdrawal', 'transfer_in', 'transfer_out', 'fundraising_transfer', 'loan_disbursement']);
            })
            ->with(['transactionType', 'transactionCategory', 'statusRelation'])
            ->latest()
            ->get();

        $expenseTransactions = $this->applyTransactionFilters(
            Transaction::query(),
            $request,
            'expenses'
        )
            ->where('member_id', $member->id)
            ->whereHas('transactionType', fn ($qt) => $qt->where('name', 'withdrawal'))
            ->with(['transactionType', 'transactionCategory', 'statusRelation'])
            ->latest()
            ->get();

        $revenueTransactions = $this->applyTransactionFilters(
            Transaction::query(),
            $request,
            'revenue'
        )
            ->where('member_id', $member->id)
            ->whereHas('transactionType', fn ($qt) => $qt->whereIn('name', ['deposit', 'loan_repayment', 'loan_payment', 'repayment']))
            ->with(['transactionType', 'transactionCategory', 'statusRelation'])
            ->latest()
            ->get();

        $assetSavingsAccounts = DB::table('savings_accounts')
            ->where('member_id', $member->id)
            ->when($request->filled('assets_savings_search'), function ($q) use ($request) {
                $term = '%' . $request->input('assets_savings_search') . '%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('account_number', 'like', $term)
                        ->orWhere('account_name', 'like', $term);
                });
            })
            ->when($request->filled('assets_savings_status'), fn ($q) => $q->where('status', $request->input('assets_savings_status')))
            ->when($request->filled('assets_savings_min'), fn ($q) => $q->where('current_balance', '>=', $request->input('assets_savings_min')))
            ->when($request->filled('assets_savings_max'), fn ($q) => $q->where('current_balance', '<=', $request->input('assets_savings_max')))
            ->orderByDesc('updated_at')
            ->get();

        $assetShares = Share::query()
            ->where('member_id', $member->id)
            ->when($request->filled('assets_shares_search'), function ($q) use ($request) {
                $term = '%' . $request->input('assets_shares_search') . '%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('certificate_number', 'like', $term)
                        ->orWhere('shares_count', 'like', $term);
                });
            })
            ->when($request->filled('assets_shares_min'), fn ($q) => $q->where('current_value', '>=', $request->input('assets_shares_min')))
            ->when($request->filled('assets_shares_max'), fn ($q) => $q->where('current_value', '<=', $request->input('assets_shares_max')))
            ->latest()
            ->get();

        $liabilityLoans = Loan::query()
            ->where('member_id', $member->id)
            ->when($request->filled('liabilities_loans_search'), function ($q) use ($request) {
                $term = '%' . $request->input('liabilities_loans_search') . '%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('loan_number', 'like', $term)
                        ->orWhere('loan_number', 'like', $term);
                });
            })
            ->when($request->filled('liabilities_loans_status'), fn ($q) => $q->status($request->input('liabilities_loans_status')))
            ->when($request->filled('liabilities_loans_min'), fn ($q) => $q->where('balance_due', '>=', $request->input('liabilities_loans_min')))
            ->when($request->filled('liabilities_loans_max'), fn ($q) => $q->where('balance_due', '<=', $request->input('liabilities_loans_max')))
            ->latest()
            ->get();

        $liabilityDividends = MemberDividend::query()
            ->where('member_id', $member->id)
            ->where('status', 'pending')
            ->when($request->filled('liabilities_dividends_year'), function ($q) use ($request) {
                $q->whereHas('dividend', fn ($d) => $d->where('year', $request->input('liabilities_dividends_year')));
            })
            ->when($request->filled('liabilities_dividends_quarter'), function ($q) use ($request) {
                $q->whereHas('dividend', fn ($d) => $d->where('quarter', $request->input('liabilities_dividends_quarter')));
            })
            ->with('dividend')
            ->latest()
            ->get();
        
        $quarterlyDividends = MemberDividend::where('member_id', $member->id)
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
        
        return view('shareholder.financial', compact(
            'totalRevenue', 'totalExpenses', 'netProfit', 'totalAssets', 'totalLiabilities',
            'totalEquity', 'totalShares', 'totalDividendsPaid', 'totalDividendsPending',
            'activeLoans', 'totalLoanAmount', 'totalInterestEarned', 'avgInterestRate',
            'monthlyRevenue', 'monthlyExpenses', 'quarterlyDividends',
            'profitMargin', 'roi', 'debtToEquity', 'currentRatio',
            'deposits', 'withdrawals', 'loanPayments', 'member',
            'transactions', 'dividends', 'shares', 'loans', 'financialSummary',
            'savingsTransactions', 'expenseTransactions', 'revenueTransactions',
            'assetSavingsAccounts', 'assetShares', 'liabilityLoans', 'liabilityDividends',
            'transactionsFundraisingTotal', 'fundraisingCampaignTotals'
        ));
    }

    private function applyTransactionFilters($query, Request $request, string $prefix)
    {
        $search = $request->input("{$prefix}_search");
        if (!empty($search)) {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('transaction_number', 'like', $term)
                    ->orWhere('reference_number', 'like', $term)
                    ->orWhere('receipt_number', 'like', $term)
                    ->orWhere('description', 'like', $term);
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
