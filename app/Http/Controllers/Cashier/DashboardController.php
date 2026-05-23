<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Fundraising;
use App\Models\FundraisingStatus;
use App\Models\Transaction;
use App\Models\Member;
use App\Models\Loan;
use App\Models\LoanStatus;
use App\Services\DashboardStatsService;

class DashboardController extends Controller
{
    public function index()
    {
        $viewStats = app(DashboardStatsService::class)->get();
        $pendingStatusId = LoanStatus::query()->where('name', 'pending')->value('id');
        $activeFundraisingStatusId = FundraisingStatus::query()->where('name', 'active')->value('id');

        $stats = [
            'todayTransactions' => Transaction::whereDate('created_at', today())->count(),
            'todayDeposits' => Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'deposit'))->whereDate('created_at', today())->sum('amount'),
            'todayWithdrawals' => Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'withdrawal'))->whereDate('created_at', today())->sum('amount'),
            'todayNet' => Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'deposit'))->whereDate('created_at', today())->sum('amount') - Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'withdrawal'))->whereDate('created_at', today())->sum('amount'),
            'totalMembers' => (int) ($viewStats['total_members'] ?? Member::count()),
            'activeMembers' => Member::whereHas('transactions', function($q) { $q->whereDate('created_at', '>=', now()->subDays(30)); })->count(),
            'pendingLoans' => (int) ($viewStats['pending_loans_count'] ?? ($pendingStatusId ? Loan::where('status_id', $pendingStatusId)->count() : 0)),
            'cashOnHand' => Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'deposit'))->sum('amount') - Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'withdrawal'))->sum('amount'),
            'activeFundraisings' => $activeFundraisingStatusId ? Fundraising::where('status_id', $activeFundraisingStatusId)->count() : Fundraising::count(),
            'totalFundraisingRaised' => (float) Fundraising::sum('raised_amount'),
            'totalFundraisingTarget' => (float) Fundraising::sum('target_amount'),
        ];

        $recentTransactions = Transaction::with('member')->latest()->take(10)->get();
        $recentFundraisings = Fundraising::with('statusRelation')->latest()->take(5)->get();
        $monthlyData = $this->getMonthlyData(now()->year);
        
        return view('cashier.dashboard', compact('stats', 'recentTransactions', 'recentFundraisings', 'monthlyData'));
    }

    private function getMonthlyData($year = null)
    {
        $months = [];
        $deposits = [];
        $withdrawals = [];
        $transactions = [];
        $netCash = [];

        if ($year === 'all' || $year === null) {
            $currentYear = now()->year;
            for ($y = 2023; $y <= $currentYear; $y++) {
                $months[] = (string)$y;
                
                $yearDeposits = Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'deposit'))->whereYear('created_at', $y)->sum('amount');
                $deposits[] = round($yearDeposits / 1000000, 2);
                
                $yearWithdrawals = Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'withdrawal'))->whereYear('created_at', $y)->sum('amount');
                $withdrawals[] = round($yearWithdrawals / 1000000, 2);
                
                $yearTransactions = Transaction::whereYear('created_at', $y)->count();
                $transactions[] = $yearTransactions;
                
                $netCash[] = round(($yearDeposits - $yearWithdrawals) / 1000000, 2);
            }
        } else {
            for ($month = 1; $month <= 12; $month++) {
                $date = \Carbon\Carbon::create($year, $month, 1);
                $months[] = $date->format('M Y');
                
                $monthDeposits = Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'deposit'))
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)->sum('amount');
                $deposits[] = round($monthDeposits / 1000000, 2);
                
                $monthWithdrawals = Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'withdrawal'))
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)->sum('amount');
                $withdrawals[] = round($monthWithdrawals / 1000000, 2);
                
                $monthTransactions = Transaction::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)->count();
                $transactions[] = $monthTransactions;
                
                $netCash[] = round(($monthDeposits - $monthWithdrawals) / 1000000, 2);
            }
        }

        $depositsPrediction = $this->predictNextMonth($deposits);
        $withdrawalsPrediction = $this->predictNextMonth($withdrawals);

        return [
            'months' => $months,
            'deposits' => $deposits,
            'withdrawals' => $withdrawals,
            'transactions' => $transactions,
            'netCash' => $netCash,
            'predictions' => [
                'deposits' => $depositsPrediction,
                'withdrawals' => $withdrawalsPrediction,
            ],
            'analytics' => [
                'avgDeposits' => round(array_sum($deposits) / max(count($deposits), 1), 2),
                'avgWithdrawals' => round(array_sum($withdrawals) / max(count($withdrawals), 1), 2),
                'avgTransactions' => round(array_sum($transactions) / max(count($transactions), 1), 1),
                'totalNet' => round(array_sum($netCash), 2),
            ]
        ];
    }

    private function predictNextMonth($data)
    {
        $n = count($data);
        if ($n < 3) return end($data);
        
        $sumX = 0; $sumY = 0; $sumXY = 0; $sumX2 = 0;
        
        foreach ($data as $x => $y) {
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;
        
        return round($slope * $n + $intercept, 2);
    }

    public function getData()
    {
        try {
            $year = request('year', null);
            $data = $this->getMonthlyData($year);
            $viewStats = app(DashboardStatsService::class)->get();
            
            $query = $year && $year !== 'all' ? fn($q) => $q->whereYear('created_at', $year) : fn($q) => $q;
            
            $data['stats'] = [
                'todayTransactions' => Transaction::whereDate('created_at', today())->count(),
                'todayDeposits' => Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'deposit'))->whereDate('created_at', today())->sum('amount'),
                'todayWithdrawals' => Transaction::whereHas('transactionType', fn ($q) => $q->where('name', 'withdrawal'))->whereDate('created_at', today())->sum('amount'),
                'totalMembers' => (int) ($viewStats['total_members'] ?? Member::where($query)->count()),
                'cashOnHand' => Transaction::where($query)->whereHas('transactionType', fn ($q) => $q->where('name', 'deposit'))->sum('amount') - Transaction::where($query)->whereHas('transactionType', fn ($q) => $q->where('name', 'withdrawal'))->sum('amount'),
                'activeFundraisings' => Fundraising::whereYear('created_at', $year && $year !== 'all' ? $year : now()->year)->count(),
                'totalFundraisingRaised' => (float) Fundraising::sum('raised_amount'),
                'totalFundraisingTarget' => (float) Fundraising::sum('target_amount'),
            ];
            
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
