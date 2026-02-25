<?php

namespace App\Http\Controllers\Shareholder;

use App\Http\Controllers\Controller;
use App\Models\User;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'shareholder')->latest()->paginate(20);

        $stats = [
            'total' => User::where('role', 'shareholder')->count(),
            'active' => User::where('role', 'shareholder')->count(),
            'admins' => 0,
            'online' => 0,
        ];

        return view('shareholder.users', compact('users', 'stats'));
    }
}
