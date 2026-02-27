<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public Routes
Route::view('/', 'welcome')->name('welcome');
Route::view('/learn-more', 'learn-more')->name('learn-more');

// Authentication Routes
Route::get('/login', function () { 
    $roleStatuses = [
        'client' => \App\Models\Setting::get('role_status_client', 1),
        'shareholder' => \App\Models\Setting::get('role_status_shareholder', 1),
        'cashier' => \App\Models\Setting::get('role_status_cashier', 1),
        'td' => \App\Models\Setting::get('role_status_td', 1),
        'ceo' => \App\Models\Setting::get('role_status_ceo', 1),
        'admin' => \App\Models\Setting::get('role_status_admin', 1),
    ];
    
    $adminPhone = \Illuminate\Support\Facades\Cache::remember('ui:admin_phone:v1', now()->addMinutes(5), function () {
        return \App\Models\User::query()
            ->where('role', 'admin')
            ->whereNotNull('phone')
            ->value('phone');
    });
    
    return view('auth.login', compact('roleStatuses', 'adminPhone')); 
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', function () { 
    $roleStatuses = [
        'client' => \App\Models\Setting::get('role_status_client', 1),
        'shareholder' => \App\Models\Setting::get('role_status_shareholder', 1),
        'cashier' => \App\Models\Setting::get('role_status_cashier', 1),
        'td' => \App\Models\Setting::get('role_status_td', 1),
        'ceo' => \App\Models\Setting::get('role_status_ceo', 1),
        'admin' => \App\Models\Setting::get('role_status_admin', 1),
    ];
    
    $registrationAllowed = [
        'client' => \App\Models\Setting::get('allow_registration_client', 1),
        'shareholder' => \App\Models\Setting::get('allow_registration_shareholder', 1),
        'cashier' => \App\Models\Setting::get('allow_registration_cashier', 1),
        'td' => \App\Models\Setting::get('allow_registration_td', 1),
        'ceo' => \App\Models\Setting::get('allow_registration_ceo', 1),
        'admin' => \App\Models\Setting::get('allow_registration_admin', 1),
    ];
    
    $adminPhone = \Illuminate\Support\Facades\Cache::remember('ui:admin_phone:v1', now()->addMinutes(5), function () {
        return \App\Models\User::query()
            ->where('role', 'admin')
            ->whereNotNull('phone')
            ->value('phone');
    });
    
    return view('auth.register', compact('roleStatuses', 'registrationAllowed', 'adminPhone')); 
})->name('register');

Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

Route::get('/reset-password/{token}', function ($token) { 
    return view('auth.reset-password', ['token' => $token]); 
})->name('password.reset');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Switch active role
Route::post('/switch-role', function(\Illuminate\Http\Request $request) {
    $allowedRoles = ['client', 'shareholder', 'cashier', 'td', 'ceo', 'admin'];
    $role = strtolower((string) $request->input('role'));
    $user = auth()->user();

    if (!$user || !in_array($role, $allowedRoles, true)) {
        return response()->json(['success' => false, 'message' => 'Invalid role.'], 422);
    }

    // Respect role status settings (default active if setting missing).
    $roleStatus = (int) \App\Models\Setting::get('role_status_' . $role, 1);
    if ($roleStatus !== 1) {
        \App\Services\AuditLogService::log($user, 'role_switch_failed', 'Role switch blocked because role is inactive', [
            'requested_role' => $role,
            'current_role' => $user->role,
            'ip' => $request->ip(),
        ]);
        return response()->json(['success' => false, 'message' => 'Selected role is currently inactive.'], 403);
    }

    // Backward compatibility: allow current users.role even if user_roles table is not populated.
    if ($user->hasRole($role) || strtolower((string) $user->role) === $role) {
        $previousRole = $user->role;
        if (!$user->hasRole($role)) {
            $user->assignRole($role);
        }

        $request->session()->put('active_role', $role);
        $user->forceFill(['role' => $role])->save();

        \App\Services\AuditLogService::log($user, 'role_switch', 'User switched active role', [
            'from' => $previousRole,
            'to' => $role,
            'ip' => $request->ip(),
        ]);

        return response()->json(['success' => true, 'role' => $role]);
    }

    \App\Services\AuditLogService::log($user, 'role_switch_failed', 'Role switch denied because role is not assigned', [
        'requested_role' => $role,
        'current_role' => $user->role,
        'ip' => $request->ip(),
    ]);

    return response()->json(['success' => false, 'message' => 'You are not assigned to this role.'], 403);
})->middleware('auth')->name('switch.role');

// Unified Dashboard
Route::get('/dashboard', function () {
    try {
        $dashboardPayload = \Illuminate\Support\Facades\Cache::remember('ui:dashboard_payload:v2', now()->addSeconds(45), function () {
            $currentYear = now()->year;
            $currentMonthStart = now()->startOfMonth();
            $previousMonthStart = now()->copy()->subMonthNoOverflow()->startOfMonth();
            $previousMonthEnd = now()->copy()->subMonthNoOverflow()->endOfMonth();

            $transactionByYear = \App\Models\Transaction::query()
                ->where(function ($query) use ($currentYear) {
                    $query->whereYear('created_at', $currentYear - 1)
                        ->orWhereYear('created_at', $currentYear);
                })
                ->selectRaw('YEAR(created_at) as yr, COALESCE(SUM(amount),0) as total')
                ->groupBy('yr')
                ->pluck('total', 'yr');

            $memberSummary = \App\Models\Member::query()
                ->selectRaw('COUNT(*) as total_members, COALESCE(SUM(savings_balance),0) as savings_balance_total, COALESCE(SUM(balance),0) as balance_total, COALESCE(SUM(savings),0) as savings_total')
                ->first();

            $currentMonthMembers = \App\Models\Member::query()
                ->where('created_at', '>=', $currentMonthStart)
                ->count();
            $lastMonthMembers = \App\Models\Member::query()
                ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
                ->count();

            $projectSummary = \App\Models\Project::query()
                ->selectRaw('SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_projects, SUM(CASE WHEN status = "active" AND progress >= 80 THEN 1 ELSE 0 END) as nearing_completion, COALESCE(AVG(roi),0) as avg_roi')
                ->first();

            $loanSummary = \App\Models\Loan::query()
                ->selectRaw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_loans, SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_loans')
                ->first();

            $userSummary = \App\Models\User::query()
                ->selectRaw('COUNT(*) as total_users, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_users')
                ->first();

            $todayTransactions = \App\Models\Transaction::query()
                ->whereDate('created_at', today())
                ->count();

            $currentYearTransactions = (float) ($transactionByYear[$currentYear] ?? 0);
            $lastYearTransactions = (float) ($transactionByYear[$currentYear - 1] ?? 0);
            $growthPercentage = $lastYearTransactions > 0
                ? (($currentYearTransactions - $lastYearTransactions) / $lastYearTransactions) * 100
                : 0;

            $memberGrowth = $lastMonthMembers > 0
                ? (($currentMonthMembers - $lastMonthMembers) / $lastMonthMembers) * 100
                : 0;

            $totalAssets = (float) (($memberSummary->savings_balance_total ?? 0) + ($memberSummary->balance_total ?? 0));
            $totalSavings = (float) ($memberSummary->savings_total ?? 0);
            $activeProjects = (int) ($projectSummary->active_projects ?? 0);
            $approvedLoans = (int) ($loanSummary->approved_loans ?? 0);

            return [
                'stats' => [
                    'totalMembers' => (int) ($memberSummary->total_members ?? 0),
                    'totalAssets' => $totalAssets,
                    'activeProjects' => $activeProjects,
                    'pendingLoans' => (int) ($loanSummary->pending_loans ?? 0),
                    'memberGrowth' => number_format($memberGrowth, 1),
                    'assetGrowth' => number_format(12.8, 1),
                    'nearingCompletion' => (int) ($projectSummary->nearing_completion ?? 0),
                    'totalSavings' => $totalSavings,
                    'totalLoans' => $approvedLoans,
                    'availableFunds' => (float) ($memberSummary->balance_total ?? 0),
                    'activeInvestments' => $activeProjects,
                    'condolenceFund' => (float) ($memberSummary->savings_balance_total ?? 0) * 0.05,
                ],
                'dashboards' => [
            'client' => [
                'title' => 'Client Dashboard',
                'subtitle' => 'Personal Financial Management',
                'icon' => 'fa-user-circle',
                'gradientStart' => '#10b981',
                'gradientEnd' => '#059669',
                'color' => 'green',
                'stat' => 'UGX ' . number_format((float) ($memberSummary->savings_balance_total ?? 0) / max((int) ($memberSummary->total_members ?? 1), 1)),
                'statLabel' => 'Avg. Savings',
                'features' => [
                    'Personal savings tracking',
                    'Loan application & status',
                    'Transaction history & analytics',
                    'Savings goals & progress'
                ],
                'url' => '/client/dashboard'
            ],
            'shareholder' => [
                'title' => 'Shareholder Dashboard',
                'subtitle' => 'Portfolio & Investment Management',
                'icon' => 'fa-chart-pie',
                'gradientStart' => '#8b5cf6',
                'gradientEnd' => '#7c3aed',
                'color' => 'purple',
                'stat' => number_format((float) ($projectSummary->avg_roi ?? 0), 1) . '%',
                'statLabel' => 'Avg. ROI',
                'features' => [
                    'Portfolio performance tracking',
                    'Dividend history & projections',
                    'Investment project analytics',
                    'Market insights & trends'
                ],
                'url' => '/shareholder/dashboard'
            ],
            'cashier' => [
                'title' => 'Cashier Dashboard',
                'subtitle' => 'Financial Operations Management',
                'icon' => 'fa-cash-register',
                'gradientStart' => '#3b82f6',
                'gradientEnd' => '#2563eb',
                'color' => 'blue',
                'stat' => $todayTransactions,
                'statLabel' => 'Daily Transactions',
                'features' => [
                    'Transaction processing & approval',
                    'Loan management & disbursement',
                    'Cash flow monitoring',
                    'Financial reporting & summaries'
                ],
                'url' => '/cashier/dashboard'
            ],
            'td' => [
                'title' => 'Technical Director',
                'subtitle' => 'Project Management & Coordination',
                'icon' => 'fa-project-diagram',
                'gradientStart' => '#6366f1',
                'gradientEnd' => '#4f46e5',
                'color' => 'indigo',
                'stat' => $activeProjects,
                'statLabel' => 'Active Projects',
                'features' => [
                    'Project progress tracking',
                    'Team performance analytics',
                    'Resource allocation management',
                    'Risk assessment & mitigation'
                ],
                'url' => '/td/dashboard'
            ],
            'ceo' => [
                'title' => 'CEO Dashboard',
                'subtitle' => 'Executive Overview & Strategy',
                'icon' => 'fa-crown',
                'gradientStart' => '#374151',
                'gradientEnd' => '#1f2937',
                'color' => 'gray',
                'stat' => ($growthPercentage >= 0 ? '+' : '') . number_format($growthPercentage, 1) . '%',
                'statLabel' => 'YTD Growth',
                'features' => [
                    'Strategic initiatives tracking',
                    'Executive KPI monitoring',
                    'Market analysis & intelligence',
                    'Financial health assessment'
                ],
                'url' => '/ceo/dashboard'
            ],
            'admin' => [
                'title' => 'Admin Dashboard',
                'subtitle' => 'System Management & Control',
                'icon' => 'fa-cog',
                'gradientStart' => '#ef4444',
                'gradientEnd' => '#dc2626',
                'color' => 'red',
                'stat' => (int) ($userSummary->active_users ?? 0) . '/' . (int) ($userSummary->total_users ?? 0),
                'statLabel' => 'Active Users',
                'features' => [
                    'User management & permissions',
                    'System monitoring & logs',
                    'Database management & backup',
                    'Security alerts & maintenance'
                ],
                'url' => '/admin/dashboard'
            ]
                ],
            ];
        });
        
        $stats = $dashboardPayload['stats'];
        $dashboards = $dashboardPayload['dashboards'];
        
        return view('dashboard', compact('stats', 'dashboards'));
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->middleware('auth')->name('dashboard');

// Debug route
Route::view('/alpine-test', 'alpine-test');

Route::get('/debug-members', function() {
    $currentUser = auth()->user();
    $currentMember = $currentUser ? $currentUser->member : null;
    $currentMemberId = $currentMember ? $currentMember->member_id : null;
    
    $members = \App\Models\Member::limit(5)->get();
    
    if ($currentMemberId) {
        foreach ($members as $member) {
            if ($member->member_id !== $currentMemberId) {
                $member->unread_count = \App\Models\ChatMessage::where('sender_id', $member->member_id)
                    ->where('receiver_id', $currentMemberId)
                    ->where('is_read', false)
                    ->count();
                
                $member->last_message = \App\Models\ChatMessage::where(function($q) use ($currentMemberId, $member) {
                    $q->where(function($q2) use ($currentMemberId, $member) {
                        $q2->where('sender_id', $currentMemberId)->where('receiver_id', $member->member_id);
                    })->orWhere(function($q2) use ($currentMemberId, $member) {
                        $q2->where('sender_id', $member->member_id)->where('receiver_id', $currentMemberId);
                    });
                })->latest()->first();
            }
        }
    }
    
    return response()->json([
        'current_user_id' => $currentUser ? $currentUser->id : null,
        'current_member_id' => $currentMemberId,
        'members' => $members->map(function($m) {
            return [
                'id' => $m->id,
                'member_id' => $m->member_id,
                'name' => $m->full_name,
                'unread_count' => $m->unread_count ?? 0,
                'last_message' => $m->last_message ? [
                    'message' => $m->last_message->message,
                    'sender_id' => $m->last_message->sender_id,
                    'is_read' => $m->last_message->is_read,
                    'created_at' => $m->last_message->created_at
                ] : null
            ];
        })
    ]);
})->middleware('auth');

// Chat Routes
Route::middleware('auth')->prefix('chat')->name('chat.')->group(function () {
    Route::get('/me', [\App\Http\Controllers\ChatController::class, 'me'])->name('me');
    Route::post('/send', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('send');
    Route::get('/messages/{memberId}', [\App\Http\Controllers\ChatController::class, 'getMessagesWithMember'])->name('messages.with-member');
    Route::get('/conversations', [\App\Http\Controllers\ChatController::class, 'getConversations'])->name('conversations.list');
    Route::post('/mark-read/{memberId}', [\App\Http\Controllers\ChatController::class, 'markConversationAsRead'])->name('mark-read.member');
    Route::get('/unread-count', [\App\Http\Controllers\ChatController::class, 'unreadCount'])->name('unread-count');

    // Backward-compatible routes
    Route::get('/messages/{senderId}/{receiverId}', [\App\Http\Controllers\ChatController::class, 'getMessages'])->name('messages');
    Route::get('/conversations/{memberId}', [\App\Http\Controllers\ChatController::class, 'getConversations'])->name('conversations');
    Route::post('/mark-read', [\App\Http\Controllers\ChatController::class, 'markAsRead'])->name('mark-read');
});

// Include role-specific routes
require __DIR__.'/admin.php';
require __DIR__.'/ceo.php';
require __DIR__.'/td.php';
require __DIR__.'/cashier.php';
require __DIR__.'/shareholder.php';
require __DIR__.'/member.php';

// Client alias for member routes
Route::prefix('client')->name('client.')->middleware(['auth', 'role:client'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('member.dashboard');
    })->name('dashboard');
});
