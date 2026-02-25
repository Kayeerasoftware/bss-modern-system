<?php

namespace App\Http\Controllers\TD;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalProjects' => Project::count(),
            'activeProjects' => Project::where('status', 'active')->count(),
            'completedProjects' => Project::where('status', 'completed')->count(),
            'pendingProjects' => Project::where('status', 'pending')->count(),
            'totalMembers' => Member::count(),
            'activeMembers' => Member::where('status', 'active')->count(),
            'totalLoans' => Loan::count(),
            'approvedLoans' => Loan::where('status', 'approved')->count(),
            'totalTransactions' => Transaction::count(),
            'todayTransactions' => Transaction::whereDate('created_at', today())->count(),
            'totalUsers' => User::count(),
            'activeUsers' => User::where('is_active', true)->count(),
        ];
        
        $recentProjects = Project::latest()->take(5)->get();
        $recentMembers = Member::select('id', 'member_id', 'full_name', 'profile_picture', 'created_at')
            ->latest()
            ->take(5)
            ->get();
        
        return view('td.dashboard', compact('stats', 'recentProjects', 'recentMembers'));
    }
}
