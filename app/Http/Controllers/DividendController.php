<?php

namespace App\Http\Controllers;

use App\Models\Dividend;
use App\Models\Member;
use Illuminate\Http\Request;

class DividendController extends Controller
{
    public function index()
    {
        $dividends = Dividend::with('member')->orderBy('year', 'desc')->orderBy('quarter', 'desc')->get();
        return response()->json($dividends);
    }

    public function store(Request $request)
    {
        $dividend = Dividend::create([
            'member_id' => $request->member_id,
            'amount' => $request->amount,
            'year' => $request->year,
            'quarter' => $request->quarter,
            'status' => 'pending'
        ]);

        return response()->json($dividend->load('member'));
    }

    public function payDividend($id)
    {
        $dividend = Dividend::findOrFail($id);
        $dividend->update([
            'status' => 'paid',
            'paid_date' => now()
        ]);

        // Update member savings (dividends increase savings)
        $member = Member::find($dividend->member_id);
        $member->increment('savings', $dividend->amount);

        return response()->json($dividend);
    }

    public function calculateDividends(Request $request)
    {
        $totalProfit = $request->total_profit;
        $year = $request->year;
        $quarter = $request->quarter;

        $members = Member::with('shares')->get();
        $totalShares = $members->sum('total_shares');

        foreach ($members as $member) {
            if ($member->total_shares > 0) {
                $dividendAmount = ($member->total_shares / $totalShares) * $totalProfit;

                Dividend::create([
                    'member_id' => $member->member_id,
                    'amount' => $dividendAmount,
                    'year' => $year,
                    'quarter' => $quarter,
                    'status' => 'pending'
                ]);
            }
        }

        return response()->json(['message' => 'Dividends calculated and created successfully']);
    }
}
