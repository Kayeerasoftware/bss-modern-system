<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Share;
use App\Models\Dividend;
use App\Models\Project;
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
        $totalLiabilities = $financialSummary['loan_outstanding'] ?? 0;
        $totalEquity = $totalAssets - $totalLiabilities;
        
        $totalShares = Share::where('member_id', $member->id)->sum(DB::raw('shares_owned * share_value')) ?: 0;
        $totalDividendsPaid = Dividend::where('member_id', $member->member_id)->where('status', 'paid')->sum('amount') ?: 0;
        $totalDividendsPending = Dividend::where('member_id', $member->member_id)->where('status', 'pending')->sum('amount') ?: 0;
        
        $activeLoans = Loan::where('member_id', $member->member_id)->where('status', 'approved')->count();
        $totalLoanAmount = Loan::where('member_id', $member->member_id)->where('status', 'approved')->sum('amount') ?: 0;
        $totalInterestEarned = Loan::where('member_id', $member->member_id)->where('status', 'approved')->sum('interest') ?: 0;
        $avgInterestRate = Loan::where('member_id', $member->member_id)->where('status', 'approved')->avg('interest_rate') ?: 0;
        
        $monthlyData = Transaction::where('member_id', $member->member_id)
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw('MONTH(created_at) as month, type, SUM(amount) as total')
            ->groupBy('month', 'type')
            ->get();
        
        $monthlyRevenue = array_fill(1, 12, 0);
        $monthlyExpenses = array_fill(1, 12, 0);
        
        foreach ($monthlyData as $data) {
            if (in_array($data->type, ['deposit', 'loan_payment'])) {
                $monthlyRevenue[$data->month] = $data->total;
            } elseif ($data->type == 'withdrawal') {
                $monthlyExpenses[$data->month] = $data->total;
            }
        }
        
        // Transactions with search
        $transactionsQuery = Transaction::where('member_id', $member->member_id);
        
        if ($request->filled('search')) {
            $transactionsQuery->where(function($q) use ($request) {
                $q->where('type', 'like', "%{$request->search}%")
                  ->orWhere('amount', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('type')) $transactionsQuery->where('type', $request->type);
        if ($request->filled('date_from')) $transactionsQuery->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $transactionsQuery->whereDate('created_at', '<=', $request->date_to);
        if ($request->filled('amount_min')) $transactionsQuery->where('amount', '>=', $request->amount_min);
        
        if ($request->sort == 'amount_high') $transactionsQuery->orderBy('amount', 'desc');
        elseif ($request->sort == 'amount_low') $transactionsQuery->orderBy('amount', 'asc');
        elseif ($request->sort == 'oldest') $transactionsQuery->oldest();
        else $transactionsQuery->latest();
        
        $transactions = $transactionsQuery->paginate($request->per_page ?? 10)->appends($request->except('page'));
        
        // Dividends with search
        $dividendsQuery = Dividend::where('member_id', $member->member_id);
        
        if ($request->filled('search')) {
            $dividendsQuery->where(function($q) use ($request) {
                $q->where('amount', 'like', "%{$request->search}%")
                  ->orWhere('year', 'like', "%{$request->search}%")
                  ->orWhere('quarter', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('status')) $dividendsQuery->where('status', $request->status);
        if ($request->filled('date_from')) $dividendsQuery->whereDate('payment_date', '>=', $request->date_from);
        if ($request->filled('date_to')) $dividendsQuery->whereDate('payment_date', '<=', $request->date_to);
        if ($request->filled('amount_min')) $dividendsQuery->where('amount', '>=', $request->amount_min);
        
        if ($request->sort == 'amount_high') $dividendsQuery->orderBy('amount', 'desc');
        elseif ($request->sort == 'amount_low') $dividendsQuery->orderBy('amount', 'asc');
        elseif ($request->sort == 'oldest') $dividendsQuery->oldest();
        else $dividendsQuery->latest();
        
        $dividends = $dividendsQuery->paginate($request->per_page ?? 10)->appends($request->except('page'));
        
        // Shares with search
        $sharesQuery = Share::where('member_id', $member->id);
        
        if ($request->filled('search')) {
            $sharesQuery->where(function($q) use ($request) {
                $q->where('certificate_number', 'like', "%{$request->search}%")
                  ->orWhere('shares_owned', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('status')) $sharesQuery->where('status', $request->status);
        if ($request->filled('date_from')) $sharesQuery->whereDate('purchase_date', '>=', $request->date_from);
        if ($request->filled('date_to')) $sharesQuery->whereDate('purchase_date', '<=', $request->date_to);
        if ($request->filled('value_min')) $sharesQuery->where('share_value', '>=', $request->value_min);
        
        if ($request->sort == 'value_high') $sharesQuery->orderBy('share_value', 'desc');
        elseif ($request->sort == 'value_low') $sharesQuery->orderBy('share_value', 'asc');
        elseif ($request->sort == 'oldest') $sharesQuery->oldest();
        else $sharesQuery->latest();
        
        $shares = $sharesQuery->paginate($request->per_page ?? 10)->appends($request->except('page'));
        
        // Loans with search
        $loansQuery = Loan::where('member_id', $member->member_id);
        
        if ($request->filled('search')) {
            $loansQuery->where(function($q) use ($request) {
                $q->where('loan_id', 'like', "%{$request->search}%")
                  ->orWhere('amount', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('status')) $loansQuery->where('status', $request->status);
        if ($request->filled('date_from')) $loansQuery->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $loansQuery->whereDate('created_at', '<=', $request->date_to);
        if ($request->filled('amount_min')) $loansQuery->where('amount', '>=', $request->amount_min);
        
        if ($request->sort == 'amount_high') $loansQuery->orderBy('amount', 'desc');
        elseif ($request->sort == 'amount_low') $loansQuery->orderBy('amount', 'asc');
        elseif ($request->sort == 'oldest') $loansQuery->oldest();
        else $loansQuery->latest();
        
        $loans = $loansQuery->paginate($request->per_page ?? 10)->appends($request->except('page'));
        
        $quarterlyDividends = Dividend::where('member_id', $member->member_id)
            ->selectRaw('year, quarter, SUM(amount) as total')
            ->where('created_at', '>=', now()->subYear())
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
            'transactions', 'dividends', 'shares', 'loans', 'financialSummary'
        ));
    }
}
