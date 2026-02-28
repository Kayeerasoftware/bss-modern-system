<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Services\Financial\MemberFinancialSyncService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        if (!$member) {
            return redirect()->route('login')->with('error', 'Member profile not found');
        }
        
        $financialSummary = app(MemberFinancialSyncService::class)->getMemberFinancialSummary($member);

        $transactionQuery = Transaction::where('member_id', $member->member_id);
        $completedTransactionQuery = (clone $transactionQuery)->where(function ($query): void {
            $query->where('status', 'completed')
                ->orWhereNull('status');
        });

        $myLoans = Loan::where('member_id', $member->member_id)->get();
        $activeLoanStatuses = ['approved', 'active', 'disbursed'];
        $activeLoans = $myLoans->filter(function ($loan) use ($activeLoanStatuses) {
            return in_array(strtolower((string) $loan->status), $activeLoanStatuses, true);
        })->values();

        $myTransactions = (clone $transactionQuery)
            ->latest()
            ->take(10)
            ->get();
        
        $stats = [
            'mySavings' => $financialSummary['net_savings'],
            'myBalance' => $financialSummary['available_balance'],
            'myLoans' => $financialSummary['loan_outstanding'],
            'myLoanCount' => $activeLoans->count(),
            'pendingLoans' => $myLoans->where('status', 'pending')->count(),
            'totalTransactions' => (clone $transactionQuery)->count(),
            'completedTransactions' => $financialSummary['completed_transactions'],
            'totalDeposits' => $financialSummary['total_deposits'],
            'totalWithdrawals' => $financialSummary['total_withdrawals'],
            'availableAfterLoans' => $financialSummary['available_after_loans'],
            'monthlyDeposits' => (clone $completedTransactionQuery)
                ->where('type', 'deposit')
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('amount'),
            'monthlyWithdrawals' => (clone $completedTransactionQuery)
                ->where('type', 'withdrawal')
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('amount'),
            // Add shareholder-style keys for compatibility
            'totalSavings' => Member::sum('savings'),
            'totalLoan' => Member::sum('loan'),
            'netBalance' => Member::sum('savings') - Member::sum('loan'),
            'totalShares' => 0,
            'shareValue' => 0,
            'totalDividends' => 0,
            'pendingDividends' => 0,
            'portfolioValue' => 0,
            'activeProjects' => 0,
            'totalProjects' => 0,
            'totalInvestments' => 0,
            'avgROI' => 0,
            'performanceRate' => 0,
        ];
        
        $recentTransactions = $myTransactions;
        $recentDividends = [];
        $recentProjects = [];
        $investmentOpportunities = [];
        
        // Chart data for last 6 months
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyData[] = [
                'month' => $date->format('M'),
                'deposits' => (clone $completedTransactionQuery)
                    ->where('type', 'deposit')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount'),
                'withdrawals' => (clone $completedTransactionQuery)
                    ->where('type', 'withdrawal')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount'),
            ];
        }
        
        return view('member.dashboard', compact('stats', 'member', 'recentTransactions', 'activeLoans', 'monthlyData', 'recentDividends', 'recentProjects', 'investmentOpportunities', 'financialSummary'));
    }

    public function savings(Request $request)
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        $query = Transaction::where('member_id', $member->member_id)
            ->where('type', 'deposit');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        if ($request->filled('period')) {
            $this->applyPeriodFilter($query, (string) $request->period);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $savingsHistory = (clone $query)
            ->latest()
            ->paginate(20)
            ->appends($request->query());

        $completedQuery = (clone $query)->where(function ($q): void {
            $q->where('status', 'completed')
                ->orWhereNull('status');
        });

        $summary = [
            'total_deposits' => (float) (clone $completedQuery)->sum('amount'),
            'monthly_deposits' => (float) (clone $completedQuery)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('amount'),
            'monthly_count' => (int) (clone $completedQuery)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
            'average_deposit' => (float) ((clone $completedQuery)->avg('amount') ?? 0),
            'completed_count' => (int) (clone $completedQuery)->count(),
            'pending_count' => (int) (clone $query)->where('status', 'pending')->count(),
        ];
        $financialSummary = app(MemberFinancialSyncService::class)->getMemberFinancialSummary($member);
        
        return view('member.savings.index', compact('member', 'savingsHistory', 'summary', 'financialSummary'));
    }

    public function contact()
    {
        return view('member.support.contact');
    }

    public function faq()
    {
        return view('member.help.index');
    }

    private function applyPeriodFilter($query, string $period): void
    {
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', now()->toDateString());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case '3months':
                $query->where('created_at', '>=', now()->subMonths(3)->startOfDay());
                break;
            case '6months':
                $query->where('created_at', '>=', now()->subMonths(6)->startOfDay());
                break;
            case 'year':
                $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
                break;
            default:
                break;
        }
    }
}
