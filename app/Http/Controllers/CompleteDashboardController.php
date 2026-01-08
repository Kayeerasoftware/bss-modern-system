<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\Project;
use Illuminate\Http\Request;

class CompleteDashboardController extends Controller
{
    public function getDashboardData(Request $request)
    {
        $role = $request->query('role', 'client');
        
        $members = Member::all();
        $loans = Loan::with('member')->get();
        $transactions = Transaction::with('member')->latest()->get();
        $projects = Project::all();
        
        $totalSavings = $members->sum('savings');
        $totalLoans = $loans->where('status', 'approved')->sum('amount');
        $pendingLoans = $loans->where('status', 'pending')->values();
        
        return response()->json([
            'totalSavings' => $totalSavings,
            'totalLoans' => $totalLoans,
            'members' => $members,
            'projects' => $projects,
            'pending_loans' => $loans,
            'recent_transactions' => $transactions,
            'stats' => [
                'total_members' => $members->count(),
                'active_projects' => $projects->where('progress', '<', 100)->count(),
                'completed_projects' => $projects->where('progress', 100)->count(),
                'total_transactions' => Transaction::count()
            ]
        ]);
    }
}
