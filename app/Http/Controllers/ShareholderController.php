<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberDividend;
use App\Models\PortfolioPerformance;
use App\Models\InvestmentOpportunity;
use App\Models\Project;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Schema;

class ShareholderController extends Controller
{
    public function getPerformanceMetrics($memberId)
    {
        $resolvedId = resolve_member_id($memberId) ?? (is_numeric($memberId) ? (int) $memberId : null);
        if (!$resolvedId) {
            return response()->json([
                'current_performance' => 0,
                'benchmark_comparison' => 0,
                'portfolio_value' => 0,
                'trend' => 'stable'
            ]);
        }

        if (!Schema::hasTable('portfolio_performances')) {
            return response()->json([
                'current_performance' => 0,
                'benchmark_comparison' => 0,
                'portfolio_value' => 0,
                'trend' => 'stable'
            ]);
        }

        $performance = PortfolioPerformance::where('member_id', $resolvedId)
            ->orderBy('period', 'desc')
            ->first();
        
        return response()->json([
            'current_performance' => $performance->performance_percentage ?? 0,
            'benchmark_comparison' => $performance->benchmark_comparison ?? 0,
            'portfolio_value' => $performance->portfolio_value ?? 0,
            'trend' => $this->calculateTrend($resolvedId)
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
        $activeStatusId = DB::table('investment_statuses')->where('name', 'active')->value('id');
        $upcomingStatusId = DB::table('investment_statuses')->where('name', 'upcoming')->value('id');
        $statusIds = array_values(array_filter([$activeStatusId, $upcomingStatusId]));

        $opportunities = InvestmentOpportunity::query()
            ->when($statusIds !== [], fn ($q) => $q->whereIn('status_id', $statusIds))
            ->orderBy('launch_date', 'desc')
            ->get();
        
        return response()->json(['opportunities' => $opportunities]);
    }

    public function getPortfolioAnalytics($memberId)
    {
        $resolvedId = resolve_member_id($memberId) ?? (is_numeric($memberId) ? (int) $memberId : null);
        $member = $resolvedId ? Member::find($resolvedId) : null;
        $dividends = $resolvedId
            ? MemberDividend::where('member_id', $resolvedId)->get()
            : collect();
        $performance = ($resolvedId && Schema::hasTable('portfolio_performances'))
            ? PortfolioPerformance::where('member_id', $resolvedId)
                ->orderBy('period', 'desc')
                ->take(12)
                ->get()
            : collect();
        
        return response()->json([
            'total_dividends' => $dividends->sum('amount'),
            'performance_history' => $performance,
            'roi' => $this->calculateROI($resolvedId),
            'market_comparison' => $this->getMarketComparison($resolvedId)
        ]);
    }

    private function calculateTrend($memberId)
    {
        if (!Schema::hasTable('portfolio_performances')) {
            return 'stable';
        }

        $recent = PortfolioPerformance::where('member_id', $memberId)
            ->orderBy('period', 'desc')
            ->take(3)
            ->pluck('performance_percentage');
        
        if ($recent->count() < 2) return 'stable';
        
        return $recent[0] > $recent[1] ? 'up' : 'down';
    }

    private function calculateROI($memberId)
    {
        if (!$memberId) {
            return 0;
        }

        $member = Member::find($memberId);
        if (!$member) {
            return 0;
        }

        $dividends = MemberDividend::where('member_id', $memberId)->sum('net_amount');
        $savings = (float) DB::table('savings_accounts')->where('member_id', $memberId)->sum('current_balance');

        if ($savings == 0.0) {
            return 0;
        }

        return round(($dividends / $savings) * 100, 2);
    }

    private function getMarketComparison($memberId)
    {
        if (!Schema::hasTable('portfolio_performances')) {
            return 0;
        }

        $performance = PortfolioPerformance::where('member_id', $memberId)
            ->orderBy('period', 'desc')
            ->first();
        
        return $performance ? $performance->benchmark_comparison : 0;
    }
}
