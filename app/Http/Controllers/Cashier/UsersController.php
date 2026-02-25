<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Financial\Transaction;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'cashier')->latest()->paginate(20);

        $stats = [
            'total' => User::where('role', 'cashier')->count(),
            'active' => User::where('role', 'cashier')->count(),
            'total_transactions' => Transaction::whereIn('processed_by', User::where('role', 'cashier')->pluck('id'))->count(),
            'today_activity' => Transaction::whereIn('processed_by', User::where('role', 'cashier')->pluck('id'))->whereDate('created_at', today())->count(),
        ];

        return view('cashier.users.index', compact('users', 'stats'));
    }
}
