<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->whereHas('member', function ($memberQuery) {
                $memberQuery->whereHas('roles', fn ($roleQuery) => $roleQuery->where('name', 'cashier'));
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->where('member_account_number', 'like', "%{$search}%")
                            ->orWhere('member_number', 'like', "%{$search}%")
                            ->orWhere('full_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active' ? 'active' : 'inactive');
        }

        $statsBaseQuery = clone $query;
        $users = $query->latest()->paginate(20)->appends($request->query());

        $cashierIds = (clone $statsBaseQuery)->pluck('id');
        $stats = [
            'total' => (clone $statsBaseQuery)->count(),
            'active' => (clone $statsBaseQuery)->where('status', 'active')->count(),
            'total_transactions' => Transaction::whereIn('processed_by', $cashierIds)->count(),
            'today_activity' => Transaction::whereIn('processed_by', $cashierIds)->whereDate('created_at', today())->count(),
        ];

        return view('cashier.users.index', compact('users', 'stats'));
    }
}
