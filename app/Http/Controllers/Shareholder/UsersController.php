<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'shareholder');

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
            'total' => User::where('role', 'shareholder')->count(),
            'active' => User::where('role', 'shareholder')->where('is_active', true)->count(),
            'admins' => 0,
            'online' => 0,
        ];

        return view('shareholder.users', compact('users', 'stats'));
    }
}
