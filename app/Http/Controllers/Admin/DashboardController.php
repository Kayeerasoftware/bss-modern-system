<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use App\Models\Fundraising;
use App\Models\Loans\LoanApplication;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalMembers' => Member::count(),
            'newMembersThisMonth' => Member::whereMonth('created_at', now()->month)->count(),
            'totalUsers' => User::count(),
            'activeUsers' => User::where('is_active', true)->count(),
            'totalLoans' => Loan::count(),
            'pendingLoans' => Loan::where('status', 'pending')->count(),
            'approvedLoans' => Loan::where('status', 'approved')->count(),
            'totalLoanAmount' => Loan::sum('amount'),
            'pendingApplications' => LoanApplication::where('status', 'pending')->count(),
            'totalTransactions' => Transaction::count(),
            'todayTransactions' => Transaction::whereDate('created_at', today())->count(),
            'totalProjects' => Project::count(),
            'activeProjects' => Project::where('status', 'active')->count(),
            'activeFundraisings' => Fundraising::where('status', 'active')->count(),
            'totalFundraisingTarget' => Fundraising::where('status', 'active')->sum('target_amount'),
            'totalFundraisingRaised' => Fundraising::where('status', 'active')->sum('raised_amount'),
            'totalAssets' => Member::sum('savings_balance') + Member::sum('balance'),
            'totalSavings' => Member::sum('savings_balance'),
        ];

        $recentMembers = Member::select('id', 'member_id', 'full_name', 'profile_picture', 'created_at')
            ->latest()
            ->take(5)
            ->get();
        $recentLoans = Loan::with('member')->latest()->take(5)->get();
        $recentTransactions = Transaction::latest()->take(5)->get();
        $monthlyData = $this->getMonthlyData(now()->year);
        
        return view('admin.dashboard', compact('stats', 'recentMembers', 'recentLoans', 'recentTransactions', 'monthlyData'));
    }

    private function getMonthlyData($year = null)
    {
        $months = [];
        $members = [];
        $loans = [];
        $transactions = [];
        $revenue = [];
        $expenses = [];
        $loanAmounts = [];
        $savingsGrowth = [];
        $memberRetention = [];
        $loanRepaymentRate = [];

        if ($year === 'all' || $year === null) {
            // Show yearly data from 2023 to current year
            $currentYear = now()->year;
            for ($y = 2023; $y <= $currentYear; $y++) {
                $months[] = (string)$y;
                
                $yearMembers = Member::whereYear('created_at', $y)->count();
                $members[] = $yearMembers;
                
                $yearLoans = Loan::whereYear('created_at', $y)->count();
                $loans[] = $yearLoans;
                
                $yearTransactions = Transaction::whereYear('created_at', $y)->count();
                $transactions[] = $yearTransactions;
                
                $yearRevenue = Transaction::whereYear('created_at', $y)
                    ->where('type', 'deposit')->sum('amount');
                $revenue[] = round($yearRevenue / 1000000, 2);
                
                $yearExpenses = Transaction::whereYear('created_at', $y)
                    ->where('type', 'withdrawal')->sum('amount');
                $expenses[] = round($yearExpenses / 1000000, 2);
                
                $yearLoanAmount = Loan::whereYear('created_at', $y)->sum('amount');
                $loanAmounts[] = round($yearLoanAmount / 1000000, 2);
                
                $savingsGrowth[] = 0;
                $memberRetention[] = 50;
                $loanRepaymentRate[] = 75;
            }
        } else {
            // Show all 12 months of selected year
            for ($month = 1; $month <= 12; $month++) {
                $date = \Carbon\Carbon::create($year, $month, 1);
                $months[] = $date->format('M Y');
                
                $monthMembers = Member::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)->count();
                $members[] = $monthMembers;
                
                $monthLoans = Loan::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)->count();
                $loans[] = $monthLoans;
                
                $monthTransactions = Transaction::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)->count();
                $transactions[] = $monthTransactions;
                
                $monthRevenue = Transaction::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('type', 'deposit')->sum('amount');
                $revenue[] = round($monthRevenue / 1000000, 2);
                
                $monthExpenses = Transaction::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('type', 'withdrawal')->sum('amount');
                $expenses[] = round($monthExpenses / 1000000, 2);
                
                $monthLoanAmount = Loan::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)->sum('amount');
                $loanAmounts[] = round($monthLoanAmount / 1000000, 2);
                
                $savingsGrowth[] = 0;
                $memberRetention[] = 50;
                $loanRepaymentRate[] = 75;
            }
        }

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
                'avgMemberGrowth' => round(array_sum($members) / count($members), 1),
                'avgLoanGrowth' => round(array_sum($loans) / count($loans), 1),
                'avgRevenue' => round(array_sum($revenue) / count($revenue), 2),
                'profitMargin' => round((array_sum($revenue) - array_sum($expenses)) / max(array_sum($revenue), 1) * 100, 1),
                'memberChurnRate' => round(100 - (array_sum($memberRetention) / count($memberRetention)), 1),
                'avgRepaymentRate' => round(array_sum($loanRepaymentRate) / count($loanRepaymentRate), 1),
            ]
        ];
    }

    private function predictNextMonth($data)
    {
        $n = count($data);
        if ($n < 3) return end($data);
        
        // Linear regression for prediction
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
            
            // Add year-specific aggregate stats for circular charts
            $query = $year && $year !== 'all' ? fn($q) => $q->whereYear('created_at', $year) : fn($q) => $q;
            
            $data['stats'] = [
                'totalMembers' => Member::where($query)->count(),
                'totalLoans' => Loan::where($query)->count(),
                'totalProjects' => Project::where($query)->count(),
                'activeFundraisings' => Fundraising::where($query)->where('status', 'active')->count(),
                'approvedLoans' => Loan::where($query)->where('status', 'approved')->count(),
                'pendingLoans' => Loan::where($query)->where('status', 'pending')->count(),
                'totalSavings' => Member::where($query)->sum('savings_balance'),
                'totalLoanAmount' => Loan::where($query)->sum('amount'),
                'totalAssets' => Member::where($query)->sum('savings_balance') + Member::where($query)->sum('balance'),
            ];
            
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
