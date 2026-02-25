<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Member\DashboardController;
use App\Http\Controllers\Member\ProfileController;
use App\Http\Controllers\Member\TransactionController;
use App\Http\Controllers\Member\LoanController;
use App\Http\Controllers\Member\ProjectController;
use App\Http\Controllers\Member\DepositController;
use App\Http\Controllers\Member\WithdrawalController;
use App\Http\Controllers\Member\NotificationController;
use App\Http\Controllers\Member\SettingsController;
use App\Http\Controllers\Member\ReportController;
use App\Http\Controllers\Member\DocumentController;

Route::prefix('member')->name('member.')->middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Bio Data
    Route::get('/bio-data/create', [ProfileController::class, 'createBioData'])->name('bio-data.create');
    Route::post('/bio-data', [ProfileController::class, 'storeBioData'])->name('bio-data.store');
    Route::get('/bio-data/view', [ProfileController::class, 'viewBioData'])->name('bio-data.view');
    
    // Transactions
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/history', [TransactionController::class, 'history'])->name('history');
        Route::get('/statement', [TransactionController::class, 'statement'])->name('statement');
        Route::post('/statement/generate', [TransactionController::class, 'generateStatement'])->name('statement.generate');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('show');
    });
    
    // Deposits
    Route::prefix('deposits')->name('deposits.')->group(function () {
        Route::get('/', [DepositController::class, 'index'])->name('index');
        Route::get('/create', [DepositController::class, 'create'])->name('create');
        Route::post('/', [DepositController::class, 'store'])->name('store');
    });
    
    // Withdrawals
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [WithdrawalController::class, 'index'])->name('index');
        Route::get('/create', [WithdrawalController::class, 'create'])->name('create');
        Route::post('/', [WithdrawalController::class, 'store'])->name('store');
    });
    
    // Loans
    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/apply', [LoanController::class, 'apply'])->name('apply');
        Route::post('/', [LoanController::class, 'store'])->name('store');
        Route::get('/my-loans', [LoanController::class, 'myLoans'])->name('my-loans');
        Route::get('/{id}', [LoanController::class, 'show'])->name('show');
        Route::get('/{id}/repay', [LoanController::class, 'repay'])->name('repay');
        Route::post('/{id}/repayment', [LoanController::class, 'storeRepayment'])->name('repayment.store');
    });
    
    // Projects
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/my-projects', [ProjectController::class, 'myProjects'])->name('my-projects');
        Route::get('/{id}', [ProjectController::class, 'show'])->name('show');
    });
    
    // Savings
    Route::get('/savings', [DashboardController::class, 'savings'])->name('savings');
    
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/{id}', [NotificationController::class, 'show'])->name('show');
        Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::post('/preferences', [NotificationController::class, 'updatePreferences'])->name('preferences');
    });
    
    // Documents
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::post('/', [DocumentController::class, 'store'])->name('store');
        Route::get('/{id}', [DocumentController::class, 'show'])->name('show');
        Route::delete('/{id}', [DocumentController::class, 'destroy'])->name('destroy');
    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/', [SettingsController::class, 'update'])->name('update');
    });
    
    // Password
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/statement', [ReportController::class, 'statement'])->name('statement');
        Route::get('/loans', [ReportController::class, 'loans'])->name('loans');
        Route::get('/tax', [ReportController::class, 'tax'])->name('tax');
    });
    
    // Help & Support
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/contact', [DashboardController::class, 'contact'])->name('contact');
        Route::get('/faq', [DashboardController::class, 'faq'])->name('faq');
    });
});
