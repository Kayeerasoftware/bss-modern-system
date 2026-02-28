<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ClientDashboardController;
use App\Http\Controllers\Api\AdminController;
// use App\Http\Controllers\Api\CEOController;
use App\Http\Controllers\Api\TDController;
use App\Http\Controllers\Api\CashierController;
use App\Http\Controllers\Api\ShareholderController;
use App\Http\Controllers\CompleteDashboardController;

// Authentication
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [\App\Http\Controllers\AuthController::class, 'user'])->middleware('auth:sanctum');

// Admin API
Route::prefix('admin')->name('api.admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/stats', [AdminController::class, 'getStats']);
    Route::get('/members', [AdminController::class, 'getMembers']);
    Route::get('/loans', [AdminController::class, 'getLoans']);
    Route::get('/transactions', [AdminController::class, 'getTransactions']);
    Route::get('/projects', [AdminController::class, 'getProjects']);
    Route::get('/financial-summary', [AdminController::class, 'getFinancialSummary']);
    Route::get('/settings', [AdminController::class, 'getSettings']);
    Route::post('/settings', [AdminController::class, 'updateSettings']);
    Route::get('/audit-logs', [AdminController::class, 'getAuditLogs']);
    Route::get('/system/health', [AdminController::class, 'getSystemHealth']);
});

// CEO API
// Route::prefix('ceo')->name('api.ceo.')->group(function () {
//     Route::get('/dashboard', [CEOController::class, 'dashboard']);
//     Route::get('/stats', [CEOController::class, 'getStats']);
//     Route::get('/financial', [CEOController::class, 'getFinancialData']);
//     Route::get('/performance', [CEOController::class, 'getPerformanceMetrics']);
// });

// TD API
Route::prefix('td')->name('api.td.')->group(function () {
    Route::get('/dashboard', [TDController::class, 'dashboard']);
    Route::get('/projects', [TDController::class, 'getProjects']);
    Route::put('/projects/{id}/progress', [TDController::class, 'updateProgress']);
});

// Cashier API
Route::prefix('cashier')->name('api.cashier.')->group(function () {
    Route::get('/dashboard', [CashierController::class, 'dashboard']);
    Route::get('/transactions/recent', [CashierController::class, 'getRecentTransactions']);
    Route::post('/deposits', [CashierController::class, 'processDeposit']);
    Route::post('/withdrawals', [CashierController::class, 'processWithdrawal']);
});

// Shareholder API
Route::prefix('shareholder')->name('api.shareholder.')->group(function () {
    Route::get('/dashboard', [ShareholderController::class, 'dashboard']);
    Route::get('/portfolio', [ShareholderController::class, 'getPortfolio']);
    Route::get('/investments', [ShareholderController::class, 'getInvestments']);
    Route::post('/investments', [ShareholderController::class, 'makeInvestment']);
    Route::get('/dividends', [ShareholderController::class, 'getDividends']);
});

// Member/Client API
Route::prefix('member')->name('api.member.')->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'getClientData']);
    Route::get('/balance', [ClientDashboardController::class, 'getBalance']);
    Route::get('/transactions', [ClientDashboardController::class, 'getTransactions']);
    Route::post('/deposits', [ClientDashboardController::class, 'makeDeposit']);
    Route::post('/withdrawals', [ClientDashboardController::class, 'requestWithdrawal']);
    Route::get('/loans', [ClientDashboardController::class, 'getLoans']);
    Route::get('/savings-history', [ClientDashboardController::class, 'getSavingsHistory']);
});

// Loans
Route::prefix('loans')->name('api.loans.')->group(function () {
    Route::get('/', [LoanController::class, 'index']);
    Route::post('/', [LoanController::class, 'store']);
    Route::get('/{id}', [LoanController::class, 'show']);
    Route::put('/{id}', [LoanController::class, 'update']);
    Route::delete('/{id}', [LoanController::class, 'destroy']);
    Route::post('/{id}/approve', [LoanController::class, 'approve']);
    Route::post('/{id}/reject', [LoanController::class, 'reject']);
    Route::post('/{id}/repayment', [LoanController::class, 'recordRepayment']);
});

// Members
Route::prefix('members')->name('api.members.')->group(function () {
    Route::get('/', [MemberController::class, 'index']);
    Route::post('/', [MemberController::class, 'store']);
    Route::get('/{id}', [MemberController::class, 'show']);
    Route::put('/{id}', [MemberController::class, 'update']);
    Route::delete('/{id}', [MemberController::class, 'destroy']);
    Route::post('/import', [MemberController::class, 'import']);
    Route::get('/export', [MemberController::class, 'export']);
    Route::get('/search/{memberId}', [MemberController::class, 'searchById'])->middleware('throttle:search');
    
    // Picture Management API
    Route::prefix('{memberId}/pictures')->name('pictures.')->group(function () {
        Route::post('/upload', [\App\Http\Controllers\Api\Members\MemberPictureController::class, 'upload'])->name('upload');
        Route::put('/update', [\App\Http\Controllers\Api\Members\MemberPictureController::class, 'update'])->name('update');
        Route::delete('/', [\App\Http\Controllers\Api\Members\MemberPictureController::class, 'delete'])->name('delete');
        Route::get('/info', [\App\Http\Controllers\Api\Members\MemberPictureController::class, 'show'])->name('info');
    });
    
    Route::post('/pictures/bulk-upload', [\App\Http\Controllers\Api\Members\MemberPictureController::class, 'bulkUpload'])->name('pictures.bulk-upload');
    Route::get('/pictures/statistics', [\App\Http\Controllers\Api\Members\MemberPictureController::class, 'statistics'])->name('pictures.statistics');
});

// Transactions
Route::prefix('transactions')->name('api.transactions.')->group(function () {
    Route::get('/', [TransactionController::class, 'index']);
    Route::post('/', [TransactionController::class, 'store']);
    Route::get('/{id}', [TransactionController::class, 'show']);
});

// Projects
Route::prefix('projects')->name('api.projects.')->group(function () {
    Route::get('/', [ProjectController::class, 'index']);
    Route::post('/', [ProjectController::class, 'store']);
    Route::get('/{id}', [ProjectController::class, 'show']);
    Route::put('/{id}', [ProjectController::class, 'update']);
    Route::delete('/{id}', [ProjectController::class, 'destroy']);
});

// Reports
Route::prefix('reports')->name('api.reports.')->group(function () {
    Route::post('/generate', [DashboardController::class, 'generateReport']);
    Route::get('/status/{id}', [DashboardController::class, 'getReportStatus']);
    Route::get('/download/{id}', [DashboardController::class, 'downloadReport']);
});

// Notifications
Route::prefix('notifications')->name('api.notifications.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/', [\App\Http\Controllers\Api\NotificationController::class, 'send']);
    Route::post('/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
});

// System
Route::prefix('system')->name('api.system.')->group(function () {
    Route::get('/health', [\App\Http\Controllers\Api\SystemHealthController::class, 'getHealth']);
    Route::post('/clear-cache', [\App\Http\Controllers\Api\SystemHealthController::class, 'clearCache']);
    Route::get('/backups', [\App\Http\Controllers\Api\BackupController::class, 'index']);
    Route::post('/backups', [\App\Http\Controllers\Api\BackupController::class, 'create']);
});

// Locations
Route::prefix('locations')->name('api.locations.')->middleware('throttle:locations')->group(function () {
    Route::get('/regions', [\App\Http\Controllers\Api\LocationController::class, 'getRegions']);
    Route::get('/districts/{region}', [\App\Http\Controllers\Api\LocationController::class, 'getDistricts']);
    Route::get('/counties/{district}', [\App\Http\Controllers\Api\LocationController::class, 'getCounties']);
    Route::get('/subcounties', [\App\Http\Controllers\Api\LocationController::class, 'getSubcounties']);
    Route::get('/parishes', [\App\Http\Controllers\Api\LocationController::class, 'getParishes']);
    Route::get('/villages', [\App\Http\Controllers\Api\LocationController::class, 'getVillages']);
});

// Current Member
Route::get('/current-member', function (Request $request) {
    $user = auth()->user();
    if (!$user || !$user->member) {
        return response()->json(['error' => 'Member not found'], 404);
    }
    return response()->json([
        'member_id' => $user->member->member_id,
        'full_name' => $user->member->full_name,
        'role' => $user->role
    ]);
})->middleware('auth:sanctum');
