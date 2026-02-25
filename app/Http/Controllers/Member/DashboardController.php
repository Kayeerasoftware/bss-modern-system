<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        if (!$member) {
            return redirect()->route('login')->with('error', 'Member profile not found');
        }
        
        // Personal stats
        $myLoans = Loan::where('member_id', $member->member_id)->get();
        $myTransactions = Transaction::where('member_id', $member->member_id)
            ->latest()
            ->take(10)
            ->get();
        
        $stats = [
            'mySavings' => $member->savings ?? 0,
            'myBalance' => $member->balance ?? 0,
            'myLoans' => $myLoans->where('status', 'approved')->sum('amount'),
            'myLoanCount' => $myLoans->where('status', 'approved')->count(),
            'pendingLoans' => $myLoans->where('status', 'pending')->count(),
            'totalTransactions' => Transaction::where('member_id', $member->member_id)->count(),
            'monthlyDeposits' => Transaction::where('member_id', $member->member_id)
                ->where('type', 'deposit')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'monthlyWithdrawals' => Transaction::where('member_id', $member->member_id)
                ->where('type', 'withdrawal')
                ->whereMonth('created_at', now()->month)
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
        $activeLoans = $myLoans->where('status', 'approved');
        $recentDividends = [];
        $recentProjects = [];
        $investmentOpportunities = [];
        
        // Chart data for last 6 months
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyData[] = [
                'month' => $date->format('M'),
                'deposits' => Transaction::where('member_id', $member->member_id)
                    ->where('type', 'deposit')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount'),
                'withdrawals' => Transaction::where('member_id', $member->member_id)
                    ->where('type', 'withdrawal')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount'),
            ];
        }
        
        return view('member.dashboard', compact('stats', 'member', 'recentTransactions', 'activeLoans', 'monthlyData', 'recentDividends', 'recentProjects', 'investmentOpportunities'));
    }

    public function savings()
    {
        $user = Auth::user();
        $member = $user->member ?? Member::where('email', $user->email)->first();
        
        $savingsHistory = Transaction::where('member_id', $member->member_id)
            ->where('type', 'deposit')
            ->latest()
            ->paginate(20);
        
        return view('member.savings.index', compact('member', 'savingsHistory'));
    }

    public function contact()
    {
        return view('member.support.contact');
    }

    public function faq()
    {
        return view('member.help.index');
    }
}
