<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Dividend;
use App\Models\PortfolioPerformance;
use App\Models\InvestmentOpportunity;
use App\Models\Project;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShareholderController extends Controller
{
    public function getPerformanceMetrics($memberId)
    {
        $performance = PortfolioPerformance::where('member_id', $memberId)
            ->orderBy('period', 'desc')
            ->first();
        
        return response()->json([
            'current_performance' => $performance->performance_percentage ?? 0,
            'benchmark_comparison' => $performance->benchmark_comparison ?? 0,
            'portfolio_value' => $performance->portfolio_value ?? 0,
            'trend' => $this->calculateTrend($memberId)
        ]);
    }

    public function getDividendAnnouncements()
    {
        $upcoming = Dividend::where('status', 'pending')
            ->where('payment_date', '>', Carbon::now())
            ->orderBy('payment_date', 'asc')
            ->get();
        
        return response()->json([
            'announcements' => $upcoming,
            'next_payment' => $upcoming->first()
        ]);
    }

    public function getInvestmentOpportunities()
    {
        $opportunities = InvestmentOpportunity::where('status', 'active')
            ->orWhere('status', 'upcoming')
            ->orderBy('launch_date', 'desc')
            ->get();
        
        return response()->json(['opportunities' => $opportunities]);
    }

    public function getPortfolioAnalytics($memberId)
    {
        $member = Member::where('member_id', $memberId)->first();
        $dividends = Dividend::where('member_id', $memberId)->get();
        $performance = PortfolioPerformance::where('member_id', $memberId)
            ->orderBy('period', 'desc')
            ->take(12)
            ->get();
        
        return response()->json([
            'total_dividends' => $dividends->sum('amount'),
            'performance_history' => $performance,
            'roi' => $this->calculateROI($memberId),
            'market_comparison' => $this->getMarketComparison($memberId)
        ]);
    }

    private function calculateTrend($memberId)
    {
        $recent = PortfolioPerformance::where('member_id', $memberId)
            ->orderBy('period', 'desc')
            ->take(3)
            ->pluck('performance_percentage');
        
        if ($recent->count() < 2) return 'stable';
        
        return $recent[0] > $recent[1] ? 'up' : 'down';
    }

    private function calculateROI($memberId)
    {
        $member = Member::where('member_id', $memberId)->first();
        $dividends = Dividend::where('member_id', $memberId)->sum('amount');
        
        if (!$member || $member->savings == 0) return 0;
        
        return round(($dividends / $member->savings) * 100, 2);
    }

    private function getMarketComparison($memberId)
    {
        $performance = PortfolioPerformance::where('member_id', $memberId)
            ->orderBy('period', 'desc')
            ->first();
        
        return $performance ? $performance->benchmark_comparison : 0;
    }
}
