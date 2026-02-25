<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Member;
use App\Models\Loan;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'todayTransactions' => Transaction::whereDate('created_at', today())->count(),
            'todayDeposits' => Transaction::where('type', 'deposit')->whereDate('created_at', today())->sum('amount'),
            'todayWithdrawals' => Transaction::where('type', 'withdrawal')->whereDate('created_at', today())->sum('amount'),
            'todayNet' => Transaction::where('type', 'deposit')->whereDate('created_at', today())->sum('amount') - Transaction::where('type', 'withdrawal')->whereDate('created_at', today())->sum('amount'),
            'totalMembers' => Member::count(),
            'activeMembers' => Member::whereHas('transactions', function($q) { $q->whereDate('created_at', '>=', now()->subDays(30)); })->count(),
            'pendingLoans' => Loan::where('status', 'pending')->count(),
            'cashOnHand' => Transaction::where('type', 'deposit')->sum('amount') - Transaction::where('type', 'withdrawal')->sum('amount'),
        ];

        $recentTransactions = Transaction::with('member')->latest()->take(10)->get();
        $monthlyData = $this->getMonthlyData(now()->year);
        
        return view('cashier.dashboard', compact('stats', 'recentTransactions', 'monthlyData'));
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
                
                $yearDeposits = Transaction::where('type', 'deposit')->whereYear('created_at', $y)->sum('amount');
                $deposits[] = round($yearDeposits / 1000000, 2);
                
                $yearWithdrawals = Transaction::where('type', 'withdrawal')->whereYear('created_at', $y)->sum('amount');
                $withdrawals[] = round($yearWithdrawals / 1000000, 2);
                
                $yearTransactions = Transaction::whereYear('created_at', $y)->count();
                $transactions[] = $yearTransactions;
                
                $netCash[] = round(($yearDeposits - $yearWithdrawals) / 1000000, 2);
            }
        } else {
            for ($month = 1; $month <= 12; $month++) {
                $date = \Carbon\Carbon::create($year, $month, 1);
                $months[] = $date->format('M Y');
                
                $monthDeposits = Transaction::where('type', 'deposit')
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)->sum('amount');
                $deposits[] = round($monthDeposits / 1000000, 2);
                
                $monthWithdrawals = Transaction::where('type', 'withdrawal')
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
            
            $query = $year && $year !== 'all' ? fn($q) => $q->whereYear('created_at', $year) : fn($q) => $q;
            
            $data['stats'] = [
                'todayTransactions' => Transaction::whereDate('created_at', today())->count(),
                'todayDeposits' => Transaction::where('type', 'deposit')->whereDate('created_at', today())->sum('amount'),
                'todayWithdrawals' => Transaction::where('type', 'withdrawal')->whereDate('created_at', today())->sum('amount'),
                'totalMembers' => Member::where($query)->count(),
                'cashOnHand' => Transaction::where($query)->where('type', 'deposit')->sum('amount') - Transaction::where($query)->where('type', 'withdrawal')->sum('amount'),
            ];
            
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
