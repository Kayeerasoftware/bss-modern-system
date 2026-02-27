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
        $query = User::where('role', 'cashier');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(20)->appends($request->query());

        $stats = [
            'total' => User::where('role', 'cashier')->count(),
            'active' => User::where('role', 'cashier')->where('is_active', true)->count(),
            'total_transactions' => Transaction::whereIn('processed_by', User::where('role', 'cashier')->pluck('id'))->count(),
            'today_activity' => Transaction::whereIn('processed_by', User::where('role', 'cashier')->pluck('id'))->whereDate('created_at', today())->count(),
        ];

        return view('cashier.users.index', compact('users', 'stats'));
    }
}
