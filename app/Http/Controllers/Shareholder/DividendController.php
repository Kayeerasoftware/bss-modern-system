<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\Dividend;
use App\Models\Member;
use Illuminate\Http\Request;

class DividendController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();
        $query = Dividend::where('member_id', $member?->member_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('amount', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
                    ->orWhere('quarter', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $dividends = $query->latest()->paginate(10)->appends($request->query());
        return view('shareholder.dividends.index', compact('dividends'));
    }
}
