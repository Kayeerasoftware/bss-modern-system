<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dividend;
use App\Models\Project;
use App\Models\Share;
use Illuminate\Http\Request;

class ShareholderController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'success' => true,
            'stats' => [
                'totalShares' => Share::count(),
                'portfolioValue' => (float) Share::selectRaw('COALESCE(SUM(shares_owned * share_value), 0) as v')->value('v'),
                'totalDividends' => (float) Dividend::sum('amount'),
                'activeProjects' => Project::where('status', 'active')->count(),
            ],
        ]);
    }

    public function getPortfolio()
    {
        return response()->json([
            'success' => true,
            'portfolio' => Share::latest()->paginate(20),
        ]);
    }

    public function getInvestments()
    {
        return response()->json([
            'success' => true,
            'investments' => Project::latest()->paginate(20),
        ]);
    }

    public function makeInvestment(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'shares_owned' => 'required|numeric|min:1',
            'share_value' => 'required|numeric|min:0',
            'purchase_date' => 'nullable|date',
        ]);

        $share = Share::create([
            'member_id' => $validated['member_id'],
            'shares_owned' => $validated['shares_owned'],
            'share_value' => $validated['share_value'],
            'purchase_date' => $validated['purchase_date'] ?? now()->toDateString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Investment created successfully.',
            'investment' => $share,
        ]);
    }

    public function getDividends()
    {
        return response()->json([
            'success' => true,
            'dividends' => Dividend::latest()->paginate(20),
        ]);
    }
}
