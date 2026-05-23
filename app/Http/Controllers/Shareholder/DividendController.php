<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberDividend;
use Illuminate\Http\Request;

class DividendController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();
        $query = MemberDividend::with('dividend')
            ->where('member_id', $member?->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('net_amount', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('dividend', function ($dividendQuery) use ($search) {
                        $dividendQuery->where('year', 'like', "%{$search}%")
                            ->orWhere('quarter', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereRaw('DATE(COALESCE(paid_at, created_at)) >= ?', [$request->date_from]);
        }

        if ($request->filled('date_to')) {
            $query->whereRaw('DATE(COALESCE(paid_at, created_at)) <= ?', [$request->date_to]);
        }

        $dividends = $query->latest()->paginate(10)->appends($request->query());
        return view('shareholder.dividends.index', compact('dividends'));
    }
}
