<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::query()->with('user:id,profile_picture');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('member_account_number', 'like', "%{$search}%")
                    ->orWhere('member_number', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('primary_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $request->role));
        }

        if ($request->filled('status')) {
            $query->where('membership_status', $request->status);
        }

        $statsBaseQuery = clone $query;
        $memberStats = [
            'totalMembers' => (clone $statsBaseQuery)->count(),
            'activeMembers' => (clone $statsBaseQuery)->where('membership_status', 'active')->count(),
            'totalBalance' => (float) \Illuminate\Support\Facades\DB::table('savings_accounts')->sum('current_balance'),
            'newThisMonth' => (clone $statsBaseQuery)->where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        $members = $query->latest()->paginate(20)->appends($request->query());
        return view('cashier.members.index', compact('members', 'memberStats'));
    }

    public function show($id)
    {
        $member = Member::with('transactions')->findOrFail($id);
        return view('cashier.members.show', compact('member'));
    }
}
