<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/learn-more', function () {
    return view('learn-more');
})->name('learn-more');

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
    
    $adminPhone = \App\Models\User::where('role', 'admin')->whereNotNull('phone')->first()->phone ?? null;
    
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
    
    $adminPhone = \App\Models\User::where('role', 'admin')->whereNotNull('phone')->first()->phone ?? null;
    
    return view('auth.register', compact('roleStatuses', 'registrationAllowed', 'adminPhone')); 
})->name('register');

Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/forgot-password', function () { 
    return view('auth.forgot-password'); 
})->name('password.request');

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
        return response()->json(['success' => false, 'message' => 'Selected role is currently inactive.'], 403);
    }

    // Backward compatibility: allow current users.role even if user_roles table is not populated.
    if ($user->hasRole($role) || strtolower((string) $user->role) === $role) {
        if (!$user->hasRole($role)) {
            $user->assignRole($role);
        }

        $request->session()->put('active_role', $role);
        $user->forceFill(['role' => $role])->save();

        return response()->json(['success' => true, 'role' => $role]);
    }

    return response()->json(['success' => false, 'message' => 'You are not assigned to this role.'], 403);
})->middleware('auth')->name('switch.role');

// Unified Dashboard
Route::get('/dashboard', function () {
    try {
        // Calculate growth percentage
        $currentYearTransactions = \App\Models\Transaction::whereYear('created_at', date('Y'))->sum('amount');
        $lastYearTransactions = \App\Models\Transaction::whereYear('created_at', date('Y') - 1)->sum('amount');
        $growthPercentage = $lastYearTransactions > 0 
            ? (($currentYearTransactions - $lastYearTransactions) / $lastYearTransactions) * 100 
            : 0;
        
        // Calculate member growth
        $currentMonthMembers = \App\Models\Member::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
        $lastMonthMembers = \App\Models\Member::whereMonth('created_at', date('m') - 1)->whereYear('created_at', date('Y'))->count();
        $memberGrowth = $lastMonthMembers > 0 
            ? (($currentMonthMembers - $lastMonthMembers) / $lastMonthMembers) * 100 
            : 0;
        
        // Calculate asset growth
        $totalAssets = \App\Models\Member::sum('savings_balance') + \App\Models\Member::sum('balance');
        $assetGrowth = 12.8; // Can be calculated from historical data if available
        
        // Count nearing completion projects
        $nearingCompletion = \App\Models\Project::where('status', 'active')
            ->where('progress', '>=', 80)
            ->count();
        
        $stats = [
            'totalMembers' => \App\Models\Member::count(),
            'totalAssets' => $totalAssets,
            'activeProjects' => \App\Models\Project::where('status', 'active')->count(),
            'pendingLoans' => \App\Models\Loan::where('status', 'pending')->count(),
            'memberGrowth' => number_format($memberGrowth, 1),
            'assetGrowth' => number_format($assetGrowth, 1),
            'nearingCompletion' => $nearingCompletion,
            'totalSavings' => \App\Models\Member::sum('savings'),
            'totalLoans' => \App\Models\Loan::where('status', 'approved')->count(),
            'availableFunds' => \App\Models\Member::sum('balance'),
            'activeInvestments' => \App\Models\Project::where('status', 'active')->count(),
            'condolenceFund' => \App\Models\Member::sum('savings_balance') * 0.05,
        ];
        
        $dashboards = [
            'client' => [
                'title' => 'Client Dashboard',
                'subtitle' => 'Personal Financial Management',
                'icon' => 'fa-user-circle',
                'gradientStart' => '#10b981',
                'gradientEnd' => '#059669',
                'color' => 'green',
                'stat' => 'UGX ' . number_format(\App\Models\Member::avg('savings_balance') ?? 0),
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
                'stat' => number_format(\App\Models\Project::avg('roi') ?? 0, 1) . '%',
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
                'stat' => \App\Models\Transaction::whereDate('created_at', today())->count(),
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
                'stat' => \App\Models\Project::where('status', 'active')->count(),
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
                'stat' => \App\Models\User::where('is_active', true)->count() . '/' . \App\Models\User::count(),
                'statLabel' => 'Active Users',
                'features' => [
                    'User management & permissions',
                    'System monitoring & logs',
                    'Database management & backup',
                    'Security alerts & maintenance'
                ],
                'url' => '/admin/dashboard'
            ]
        ];
        
        return view('dashboard', compact('stats', 'dashboards'));
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->middleware('auth')->name('dashboard');

// Debug route
Route::get('/alpine-test', function() {
    return view('alpine-test');
});

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
