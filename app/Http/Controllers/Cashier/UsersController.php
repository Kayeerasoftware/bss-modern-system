<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Financial\Transaction;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->whereHas('member', function ($memberQuery) {
                $memberQuery->where('role', 'cashier');
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('member_id', 'like', "%{$search}%")
                            ->orWhere('full_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $statsBaseQuery = clone $query;
        $users = $query->latest()->paginate(20)->appends($request->query());

        $cashierIds = (clone $statsBaseQuery)->pluck('id');
        $stats = [
            'total' => (clone $statsBaseQuery)->count(),
            'active' => (clone $statsBaseQuery)->where('is_active', true)->count(),
            'total_transactions' => Transaction::whereIn('processed_by', $cashierIds)->count(),
            'today_activity' => Transaction::whereIn('processed_by', $cashierIds)->whereDate('created_at', today())->count(),
        ];

        return view('cashier.users.index', compact('users', 'stats'));
    }
}
