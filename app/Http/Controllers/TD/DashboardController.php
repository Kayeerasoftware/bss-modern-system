<?php

namespace App\Http\Controllers\TD;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Member;
use App\Models\Loan;
use App\Models\LoanStatus;
use App\Models\Transaction;
use App\Models\User;
use App\Services\DashboardStatsService;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('td_dashboard:stats:v2', now()->addSeconds(60), static function () {
            $viewStats = app(DashboardStatsService::class)->get();
            $activeStatusId    = \App\Models\ProjectStatus::query()->where('name', 'active')->value('id');
            $completedStatusId = \App\Models\ProjectStatus::query()->where('name', 'completed')->value('id');
            $pendingStatusId   = \App\Models\ProjectStatus::query()->where('name', 'pending')->value('id');

            $projectSummary = Project::query()
                ->selectRaw('
                    COUNT(*) as total_projects,
                    SUM(CASE WHEN status_id = ? THEN 1 ELSE 0 END) as active_projects,
                    SUM(CASE WHEN status_id = ? THEN 1 ELSE 0 END) as completed_projects,
                    SUM(CASE WHEN status_id = ? THEN 1 ELSE 0 END) as pending_projects
                ', [$activeStatusId, $completedStatusId, $pendingStatusId])
                ->first();

            $memberSummary = User::query()
                ->selectRaw('COUNT(*) as total_members, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_members')
                ->first();

            $approvedStatusId = LoanStatus::query()->where('name', 'approved')->value('id');
            $loanSummary = Loan::query()
                ->selectRaw('COUNT(*) as total_loans, SUM(CASE WHEN status_id = ? THEN 1 ELSE 0 END) as approved_loans', [$approvedStatusId])
                ->first();

            $txSummary = Transaction::query()
                ->selectRaw('COUNT(*) as total_transactions, SUM(CASE WHEN DATE(created_at) = CURRENT_DATE THEN 1 ELSE 0 END) as today_transactions')
                ->first();

            $userSummary = User::query()
                ->selectRaw('COUNT(*) as total_users, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_users')
                ->first();

            $linkedSummary = Member::query()
                ->selectRaw('COUNT(DISTINCT user_id) as linked_members')
                ->whereNotNull('user_id')
                ->first();

            $totalUsers = (int) ($userSummary->total_users ?? 0);
            $linkedMembers = (int) ($linkedSummary->linked_members ?? 0);

            return [
                'totalProjects' => (int) ($projectSummary->total_projects ?? 0),
                'activeProjects' => (int) ($viewStats['active_projects'] ?? $projectSummary->active_projects ?? 0),
                'completedProjects' => (int) ($projectSummary->completed_projects ?? 0),
                'pendingProjects' => (int) ($projectSummary->pending_projects ?? 0),
                'totalMembers' => (int) ($viewStats['total_members'] ?? $memberSummary->total_members ?? 0),
                'activeMembers' => (int) ($memberSummary->active_members ?? 0),
                'totalLoans' => (int) ($loanSummary->total_loans ?? 0),
                'approvedLoans' => (int) ($loanSummary->approved_loans ?? 0),
                'totalTransactions' => (int) ($txSummary->total_transactions ?? 0),
                'todayTransactions' => (int) ($txSummary->today_transactions ?? 0),
                'totalUsers' => $totalUsers,
                'activeUsers' => (int) ($userSummary->active_users ?? 0),
                'linkedMembers' => $linkedMembers,
                'unlinkedUsers' => max($totalUsers - $linkedMembers, 0),
            ];
        });

        $recentProjects = Cache::remember('td_dashboard:recent_projects:v1', now()->addSeconds(30), static function () {
            return Project::query()->latest()->take(5)->get();
        });

        $recentMembers = Cache::remember('td_dashboard:recent_members:v2', now()->addSeconds(30), static function () {
            return User::query()
                ->with([
                    'member' => static function ($query) {
                        $query->withTrashed();
                    },
                    'roleRecord',
                ])
                ->select('id', 'username', 'email', 'role_id', 'profile_picture', 'created_at')
                ->latest()
                ->take(5)
                ->get();
        });
        
        return view('td.dashboard', compact('stats', 'recentProjects', 'recentMembers'));
    }
}
