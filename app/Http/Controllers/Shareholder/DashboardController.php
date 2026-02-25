<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Dividend;
use App\Models\Share;
use App\Models\PortfolioPerformance;
use App\Models\InvestmentOpportunity;
use App\Models\Transaction;
use App\Models\Member;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();
        
        $stats = [
            'totalShares' => Share::where('member_id', $member?->member_id)->sum('shares_owned') ?? 0,
            'shareValue' => Share::where('member_id', $member?->member_id)->get()->sum(fn($s) => $s->shares_owned * $s->share_value) ?? 0,
            'totalDividends' => Dividend::where('member_id', $member?->member_id)->sum('amount') ?? 0,
            'pendingDividends' => Dividend::where('member_id', $member?->member_id)->where('status', 'pending')->sum('amount') ?? 0,
            'activeProjects' => Project::where('status', 'active')->count(),
            'totalProjects' => Project::count(),
            'totalInvestments' => Project::sum('budget') ?? 0,
            'avgROI' => Project::avg('roi') ?? 0,
            'portfolioValue' => PortfolioPerformance::where('member_id', $member?->member_id)->latest()->value('portfolio_value') ?? 0,
            'performanceRate' => PortfolioPerformance::where('member_id', $member?->member_id)->latest()->value('performance_percentage') ?? 0,
            'investmentOpportunities' => InvestmentOpportunity::where('status', 'active')->count(),
            'recentTransactions' => Transaction::where('member_id', $member?->member_id)->whereDate('created_at', '>=', now()->subDays(30))->count(),
            'totalSavings' => $member?->savings ?? 0,
            'totalLoan' => $member?->loan ?? 0,
            'netBalance' => ($member?->savings ?? 0) - ($member?->loan ?? 0),
        ];

        $recentDividends = Dividend::where('member_id', $member?->member_id)->latest()->take(5)->get();
        $recentProjects = Project::latest()->take(5)->get();
        $investmentOpportunities = InvestmentOpportunity::where('status', 'active')->latest()->take(3)->get();
        $monthlyData = $this->getMonthlyData(now()->year, $member?->member_id);
        
        return view('shareholder.dashboard', compact('stats', 'recentDividends', 'recentProjects', 'investmentOpportunities', 'monthlyData'));
    }

    private function getMonthlyData($year = null, $memberId = null)
    {
        $months = [];
        $dividends = [];
        $shareValue = [];
        $portfolioPerformance = [];
        $projectROI = [];
        $investments = [];

        if ($year === 'all' || $year === null) {
            $currentYear = now()->year;
            for ($y = 2023; $y <= $currentYear; $y++) {
                $months[] = (string)$y;
                
                $yearDividends = Dividend::where('member_id', $memberId)->whereYear('payment_date', $y)->sum('amount');
                $dividends[] = round($yearDividends / 1000000, 2);
                
                $yearShares = Share::where('member_id', $memberId)->whereYear('created_at', $y)->get()->sum(fn($s) => $s->shares_owned * $s->share_value);
                $shareValue[] = round($yearShares / 1000000, 2);
                
                $yearPerformance = PortfolioPerformance::where('member_id', $memberId)->whereYear('period', $y)->avg('performance_percentage');
                $portfolioPerformance[] = round($yearPerformance ?? 0, 2);
                
                $yearROI = Project::whereYear('created_at', $y)->avg('roi');
                $projectROI[] = round($yearROI ?? 0, 2);
                
                $yearInvestments = Project::whereYear('created_at', $y)->sum('budget');
                $investments[] = round($yearInvestments / 1000000, 2);
            }
        } else {
            for ($month = 1; $month <= 12; $month++) {
                $date = \Carbon\Carbon::create($year, $month, 1);
                $months[] = $date->format('M Y');
                
                $monthDividends = Dividend::where('member_id', $memberId)
                    ->whereYear('payment_date', $year)
                    ->whereMonth('payment_date', $month)->sum('amount');
                $dividends[] = round($monthDividends / 1000000, 2);
                
                $monthShares = Share::where('member_id', $memberId)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)->get()->sum(fn($s) => $s->shares_owned * $s->share_value);
                $shareValue[] = round($monthShares / 1000000, 2);
                
                $monthPerformance = PortfolioPerformance::where('member_id', $memberId)
                    ->whereYear('period', $year)
                    ->whereMonth('period', $month)->avg('performance_percentage');
                $portfolioPerformance[] = round($monthPerformance ?? 0, 2);
                
                $monthROI = Project::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)->avg('roi');
                $projectROI[] = round($monthROI ?? 0, 2);
                
                $monthInvestments = Project::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)->sum('budget');
                $investments[] = round($monthInvestments / 1000000, 2);
            }
        }

        $dividendsPrediction = $this->predictNextMonth($dividends);
        $shareValuePrediction = $this->predictNextMonth($shareValue);
        $performancePrediction = $this->predictNextMonth($portfolioPerformance);

        return [
            'months' => $months,
            'dividends' => $dividends,
            'shareValue' => $shareValue,
            'portfolioPerformance' => $portfolioPerformance,
            'projectROI' => $projectROI,
            'investments' => $investments,
            'predictions' => [
                'dividends' => $dividendsPrediction,
                'shareValue' => $shareValuePrediction,
                'performance' => $performancePrediction,
            ],
            'analytics' => [
                'avgDividends' => round(array_sum($dividends) / max(count($dividends), 1), 2),
                'avgPerformance' => round(array_sum($portfolioPerformance) / max(count($portfolioPerformance), 1), 2),
                'avgROI' => round(array_sum($projectROI) / max(count($projectROI), 1), 2),
                'totalInvestments' => round(array_sum($investments), 2),
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
            $user = auth()->user();
            $member = Member::where('user_id', $user->id)->first();
            $data = $this->getMonthlyData($year, $member?->member_id);
            
            $query = $year && $year !== 'all' ? fn($q) => $q->whereYear('created_at', $year) : fn($q) => $q;
            
            $data['stats'] = [
                'totalShares' => Share::where('member_id', $member?->member_id)->where($query)->sum('shares_owned') ?? 0,
                'shareValue' => Share::where('member_id', $member?->member_id)->where($query)->get()->sum(fn($s) => $s->shares_owned * $s->share_value) ?? 0,
                'totalDividends' => Dividend::where('member_id', $member?->member_id)->where($query)->sum('amount') ?? 0,
                'activeProjects' => Project::where($query)->where('status', 'active')->count(),
                'totalProjects' => Project::where($query)->count(),
                'portfolioValue' => PortfolioPerformance::where('member_id', $member?->member_id)->where($query)->latest()->value('portfolio_value') ?? 0,
            ];
            
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
