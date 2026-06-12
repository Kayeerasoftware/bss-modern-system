<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shareholder\DashboardController;
use App\Http\Controllers\Shareholder\InvestmentController;
use App\Http\Controllers\Shareholder\DividendController;
use App\Http\Controllers\Shareholder\ProjectController;
use App\Http\Controllers\Shareholder\ReportController;
use App\Http\Controllers\Shareholder\ProfileController;
use App\Http\Controllers\Shareholder\MembersController;
use App\Http\Controllers\Shareholder\LoansController;
use App\Http\Controllers\Shareholder\TransactionsController;
use App\Http\Controllers\Shareholder\FinancialController;
use App\Http\Controllers\Shareholder\UsersController;
use App\Http\Controllers\Shareholder\NotificationsController;

Route::prefix('shareholder')->name('shareholder.')->middleware(['auth', 'role:shareholder'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');
    
    // Investments
    Route::prefix('investments')->name('investments.')->group(function () {
        Route::get('/', [InvestmentController::class, 'index'])->name('index');
        Route::get('/create', [InvestmentController::class, 'create'])->name('create');
        Route::post('/', [InvestmentController::class, 'store'])->name('store');
        Route::get('/{id}', [InvestmentController::class, 'show'])->name('show');
    });
    
    // Dividends
    Route::prefix('dividends')->name('dividends.')->group(function () {
        Route::get('/', [DividendController::class, 'index'])->name('index');
    });
    
    // Projects
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/{id}', [ProjectController::class, 'show'])->name('show');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
        Route::get('/view/{id}', [ReportController::class, 'view'])->name('view');
        Route::get('/portfolio', [ReportController::class, 'portfolio'])->name('portfolio');
        Route::get('/dividends', [ReportController::class, 'dividends'])->name('dividends');
        Route::get('/performance', [ReportController::class, 'performance'])->name('performance');
        Route::get('/tax', [ReportController::class, 'tax'])->name('tax');
    });
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/picture', [ProfileController::class, 'uploadProfilePicture'])->name('profile.picture');
    
    // Bio Data
    Route::get('/bio-data/create', [ProfileController::class, 'createBioData'])->name('bio-data.create');
    Route::post('/bio-data', [ProfileController::class, 'storeBioData'])->name('bio-data.store');
    Route::get('/bio-data/view', [ProfileController::class, 'viewBioData'])->name('bio-data.view');
    
    // Additional Pages
    Route::get('/members', [MembersController::class, 'index'])->name('members');
    Route::get('/members/{id}', [MembersController::class, 'show'])->name('members.show');
    Route::get('/loans', [LoansController::class, 'index'])->name('loans');
    Route::get('/loans/applications/index', [LoansController::class, 'applications'])->name('loans.applications');
    Route::get('/loans/applications/{id}', [LoansController::class, 'showApplication'])->name('loans.applications.show');
    Route::get('/loans/apply/new', [LoansController::class, 'apply'])->name('loans.apply');
    Route::post('/loans/apply', [LoansController::class, 'storeApplication'])->name('loans.store');
    Route::get('/loans/{id}', [LoansController::class, 'show'])->name('loans.show');
    Route::post('/loans/{id}/payment', [LoansController::class, 'makePayment'])->name('loans.payment');
    Route::get('/transactions', [TransactionsController::class, 'index'])->name('transactions');
    Route::get('/transactions/create', [TransactionsController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionsController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{id}', [TransactionsController::class, 'show'])->name('transactions.show');
    Route::get('/financial', [FinancialController::class, 'index'])->name('financial');
    Route::get('/users', [UsersController::class, 'index'])->name('users');
    Route::view('/settings', 'shareholder.settings')->name('settings');
    Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications');
    Route::get('/notifications/index', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [NotificationsController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/{id}/read', [NotificationsController::class, 'markAsRead'])->name('notifications.read');
});
