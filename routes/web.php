<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BioDataController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DividendController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\CrudController;
use App\Http\Controllers\DashboardApiController;
use App\Http\Controllers\DashboardRouterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompleteDashboardController;
use App\Http\Controllers\ShareholderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;

// Public Routes
Route::get('/', function () {
    $totalMembers = \App\Models\Member::count();
    $totalAssets = \App\Models\Member::sum('savings') + \App\Models\Loan::where('status', 'approved')->sum('amount');
    $activeProjects = \App\Models\Project::where('progress', '<', 100)->count();
    
    return view('dashboard-index', compact('totalMembers', 'totalAssets', 'activeProjects'));
})->name('welcome');

Route::get('/bss', function () {
    return view('dashboard-index');
})->name('bss');

Route::get('/dashboard', function () {
    return view('complete-dashboard');
})->name('dashboard');

Route::get('/complete', function () {
    return view('complete-dashboard');
})->name('complete-dashboard');

// Role-specific Dashboards
Route::get('/client-dashboard', function () {
    return view('client-dashboard');
})->name('client-dashboard');

Route::get('/shareholder-dashboard', function () {
    return view('shareholder-dashboard');
})->name('shareholder-dashboard');

Route::get('/cashier-dashboard', function () {
    return view('cashier-dashboard');
})->name('cashier-dashboard');

Route::get('/td-dashboard', function () {
    return view('td-dashboard');
})->name('td-dashboard');

Route::get('/ceo-dashboard', function () {
    return view('ceo-dashboard');
})->name('ceo-dashboard');

Route::get('/admin-dashboard', function () {
    return view('admin-dashboard');
})->name('admin-dashboard');

// Chart Test Route
Route::get('/chart-test', function () {
    return view('chart-test');
})->name('chart-test');

// Charts Dashboard Route
Route::get('/charts', function () {
    return view('charts-dashboard');
})->name('charts-dashboard');

// Dynamic Dashboard Router
Route::get('/dashboard/{role}', [DashboardRouterController::class, 'getRoleDashboard'])->name('role-dashboard');

Route::post('/contact', function () {
    // Handle contact form submission (e.g., send email)
    return back()->with('success', 'Your message has been sent!');
})->name('contact.submit');

// Authentication Routes
Route::get('/login', function () { return view('login'); })->name('login');
Route::post('/api/login', [AuthController::class, 'login']);
Route::post('/api/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/api/user', [AuthController::class, 'user'])->middleware('auth');

// Test API Routes (no auth required)
Route::get('/api/dashboard-data', [CompleteDashboardController::class, 'getDashboardData']);
Route::get('/api/debug-data', [App\Http\Controllers\DebugController::class, 'testData']);

// Dynamic Dashboard API Routes
Route::get('/api/client-data/{memberId?}', [DashboardApiController::class, 'getClientData']);
Route::get('/api/shareholder-data/{memberId?}', [DashboardApiController::class, 'getShareholderData']);
Route::get('/api/cashier-data', [DashboardApiController::class, 'getCashierData']);
Route::get('/api/td-data', [DashboardApiController::class, 'getTdData']);
Route::get('/api/ceo-data', [DashboardApiController::class, 'getCeoData']);
Route::get('/api/admin-data', [DashboardApiController::class, 'getAdminData']);

// Shareholder Advanced Features
Route::get('/api/shareholder/performance/{memberId}', [ShareholderController::class, 'getPerformanceMetrics']);
Route::get('/api/shareholder/dividend-announcements', [ShareholderController::class, 'getDividendAnnouncements']);
Route::get('/api/shareholder/investment-opportunities', [ShareholderController::class, 'getInvestmentOpportunities']);
Route::get('/api/shareholder/portfolio-analytics/{memberId}', [ShareholderController::class, 'getPortfolioAnalytics']);

// Profile Picture Management
Route::post('/api/profile/upload-picture', [ProfileController::class, 'uploadProfilePicture']);
Route::get('/api/profile/picture/{memberId}', [ProfileController::class, 'getProfilePicture']);

// Chat Management
Route::post('/api/chat/send', [ChatController::class, 'sendMessage']);
Route::get('/api/chat/messages/{senderId}/{receiverId}', [ChatController::class, 'getMessages']);
Route::get('/api/chat/conversations/{memberId}', [ChatController::class, 'getConversations']);
Route::post('/api/chat/mark-read', [ChatController::class, 'markAsRead']);

// CRUD Operations (no auth for demo)
Route::post('/api/members', [CrudController::class, 'createMember']);
Route::put('/api/members/{id}', [CrudController::class, 'updateMember']);
Route::delete('/api/members/{id}', [CrudController::class, 'deleteMember']);
Route::get('/api/members/{memberId}/data', [CrudController::class, 'getMemberData']);

Route::post('/api/loans', [CrudController::class, 'createLoan']);
Route::put('/api/loans/{id}', [CrudController::class, 'updateLoan']);
Route::delete('/api/loans/{id}', [CrudController::class, 'deleteLoan']);
Route::post('/api/loans/{id}/approve', [CrudController::class, 'approveLoan']);
Route::post('/api/loans/{id}/reject', [CrudController::class, 'rejectLoan']);

Route::post('/api/transactions', [CrudController::class, 'createTransaction']);
Route::put('/api/transactions/{id}', [CrudController::class, 'updateTransaction']);
Route::delete('/api/transactions/{id}', [CrudController::class, 'deleteTransaction']);

Route::post('/api/projects', [CrudController::class, 'createProject']);
Route::put('/api/projects/{id}', [CrudController::class, 'updateProject']);
Route::delete('/api/projects/{id}', [CrudController::class, 'deleteProject']);

Route::post('/api/shares', [CrudController::class, 'createShare']);
Route::put('/api/shares/{id}', [CrudController::class, 'updateShare']);

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    Route::post('/api/bio-data', [BioDataController::class, 'store']);
    Route::get('/api/bio-data', [BioDataController::class, 'getData']);
    Route::get('/api/bio-data/{id}', [BioDataController::class, 'show']);
    Route::put('/api/bio-data/{id}', [BioDataController::class, 'update']);
    Route::get('/api/bio-data-stats', [BioDataController::class, 'getStats']);

    // Member management routes
    Route::get('/api/members/{id}/profile', [MemberController::class, 'getProfile']);
    Route::get('/api/members/search', [MemberController::class, 'search']);
    Route::post('/api/members/{id}/balance', [MemberController::class, 'updateBalance']);

    // Notification routes
    Route::get('/api/notifications', [NotificationController::class, 'index']);
    Route::post('/api/notifications', [NotificationController::class, 'store']);
    Route::post('/api/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // Report routes
    Route::get('/api/reports/financial', [ReportController::class, 'financialSummary']);
    Route::get('/api/reports/members', [ReportController::class, 'memberReport']);
    Route::get('/api/reports/loans', [ReportController::class, 'loanReport']);

    // Analytics routes
    Route::get('/api/analytics/dashboard', [AnalyticsController::class, 'getDashboardAnalytics']);
    Route::get('/analytics', function () { return view('analytics-dashboard'); })->name('analytics-dashboard');

    // Meeting routes
    Route::get('/api/meetings', [MeetingController::class, 'index']);
    Route::post('/api/meetings', [MeetingController::class, 'store']);
    Route::get('/api/meetings/{id}', [MeetingController::class, 'show']);
    Route::put('/api/meetings/{id}', [MeetingController::class, 'update']);
    Route::delete('/api/meetings/{id}', [MeetingController::class, 'destroy']);

    // Document routes
    Route::get('/api/documents', [DocumentController::class, 'index']);
    Route::post('/api/documents', [DocumentController::class, 'store']);
    Route::get('/api/documents/{id}', [DocumentController::class, 'show']);
    Route::put('/api/documents/{id}', [DocumentController::class, 'update']);
    Route::delete('/api/documents/{id}', [DocumentController::class, 'destroy']);
    Route::get('/api/documents/{id}/download', [DocumentController::class, 'download']);

    // Loan Management
    Route::post('/api/loans/{id}/repayment', [LoanController::class, 'repayment']);

    // Transaction Management
    Route::get('/api/transactions/member/{memberId}', [TransactionController::class, 'getByMember']);
    Route::get('/api/transactions/summary', [TransactionController::class, 'summary']);

    // Project Management
    Route::resource('/api/projects', ProjectController::class);
    Route::post('/api/projects/{id}/progress', [ProjectController::class, 'updateProgress']);


    // Settings Management
    Route::get('/api/settings', [SettingsController::class, 'getSettings']);
    Route::post('/api/settings', [SettingsController::class, 'updateSettings']);
    Route::post('/api/settings/reset', [SettingsController::class, 'resetSettings']);

    // Share Management Routes
    Route::resource('/api/shares', ShareController::class);
    Route::get('/api/shares/member/{memberId}', [ShareController::class, 'getByMember']);
    Route::post('/api/shares/transfer', [ShareController::class, 'transferShares']);
    Route::get('/api/shares/summary', [ShareController::class, 'summary']);

    // Dividend Management Routes
    Route::resource('/api/dividends', DividendController::class);
    Route::post('/api/dividends/{id}/pay', [DividendController::class, 'payDividend']);
    Route::post('/api/dividends/calculate', [DividendController::class, 'calculateDividends']);

    // User Management Routes (for authenticated users with appropriate roles)
    Route::resource('/api/users', UserController::class);
    Route::get('/api/user-roles', [UserController::class, 'getRoles']);

    // Admin Panel Routes
    Route::get('/admin', function () { return view('admin-panel'); })->name('admin');

    // Deposits Routes (for authenticated users with appropriate roles)
    Route::resource('/api/deposits', DepositController::class);
});

// System Health Check
Route::get('/api/system/health', function() {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'version' => '2.0.0',
        'admin_panel' => true,
        'features' => [
            'member_management' => true,
            'loan_processing' => true,
            'savings_tracking' => true,
            'project_management' => true,
            'share_management' => true,
            'dividend_distribution' => true,
            'meeting_scheduling' => true,
            'document_management' => true,
            'comprehensive_analytics' => true,
            'notification_system' => true,
            'admin_panel' => true
        ]
    ]);
});
