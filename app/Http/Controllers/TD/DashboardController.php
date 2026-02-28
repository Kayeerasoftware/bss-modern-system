<?php

namespace App\Http\Controllers\TD;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('td_dashboard:stats:v1', now()->addSeconds(60), static function () {
            $projectSummary = Project::query()
                ->selectRaw('COUNT(*) as total_projects, SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_projects, SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_projects, SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_projects')
                ->first();

            $memberSummary = Member::query()
                ->selectRaw('COUNT(*) as total_members, SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_members')
                ->first();

            $loanSummary = Loan::query()
                ->selectRaw('COUNT(*) as total_loans, SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_loans')
                ->first();

            $txSummary = Transaction::query()
                ->selectRaw('COUNT(*) as total_transactions, SUM(CASE WHEN DATE(created_at) = CURRENT_DATE THEN 1 ELSE 0 END) as today_transactions')
                ->first();

            $userSummary = User::query()
                ->selectRaw('COUNT(*) as total_users, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_users')
                ->first();

            return [
                'totalProjects' => (int) ($projectSummary->total_projects ?? 0),
                'activeProjects' => (int) ($projectSummary->active_projects ?? 0),
                'completedProjects' => (int) ($projectSummary->completed_projects ?? 0),
                'pendingProjects' => (int) ($projectSummary->pending_projects ?? 0),
                'totalMembers' => (int) ($memberSummary->total_members ?? 0),
                'activeMembers' => (int) ($memberSummary->active_members ?? 0),
                'totalLoans' => (int) ($loanSummary->total_loans ?? 0),
                'approvedLoans' => (int) ($loanSummary->approved_loans ?? 0),
                'totalTransactions' => (int) ($txSummary->total_transactions ?? 0),
                'todayTransactions' => (int) ($txSummary->today_transactions ?? 0),
                'totalUsers' => (int) ($userSummary->total_users ?? 0),
                'activeUsers' => (int) ($userSummary->active_users ?? 0),
            ];
        });

        $recentProjects = Cache::remember('td_dashboard:recent_projects:v1', now()->addSeconds(30), static function () {
            return Project::query()->latest()->take(5)->get();
        });

        $recentMembers = Cache::remember('td_dashboard:recent_members:v1', now()->addSeconds(30), static function () {
            return Member::query()
                ->select('id', 'user_id', 'member_id', 'full_name', 'profile_picture', 'created_at')
                ->latest()
                ->take(5)
                ->get();
        });
        
        return view('td.dashboard', compact('stats', 'recentProjects', 'recentMembers'));
    }
}
