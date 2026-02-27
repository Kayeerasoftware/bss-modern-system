<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CEO\DashboardController;
use App\Http\Controllers\CEO\MemberController;
use App\Http\Controllers\CEO\LoanController;
use App\Http\Controllers\CEO\LoanApplicationController;
use App\Http\Controllers\CEO\LoanSettingsController;
use App\Http\Controllers\CEO\TransactionController;
use App\Http\Controllers\CEO\ProjectController;
use App\Http\Controllers\CEO\FundraisingController;
use App\Http\Controllers\CEO\UserController;
use App\Http\Controllers\CEO\NotificationController;
use App\Http\Controllers\CEO\SystemController;
use App\Http\Controllers\CEO\SystemHealthController;

Route::prefix('ceo')->name('ceo.')->middleware(['web', 'auth', 'role:ceo,admin'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');
    
    // Profile
    Route::get('/profile', [\App\Http\Controllers\CEO\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\CEO\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\CEO\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/picture', [\App\Http\Controllers\CEO\ProfileController::class, 'uploadProfilePicture'])->name('profile.picture');
    Route::post('/profile/preferences', [\App\Http\Controllers\CEO\ProfileController::class, 'updatePreferences'])->name('profile.preferences');
    
    // Members
    Route::prefix('members')->name('members.')->group(function () {
        Route::get('/', [MemberController::class, 'index'])->name('index');
        Route::get('/create', [MemberController::class, 'create'])->name('create');
        Route::post('/', [MemberController::class, 'store'])->name('store');
        Route::get('/{id}', [MemberController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [MemberController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MemberController::class, 'update'])->name('update');
        Route::delete('/{id}', [MemberController::class, 'destroy'])->name('destroy');
        
        Route::post('/{id}/picture/upload', [MemberController::class, 'uploadPicture'])->name('picture.upload');
        Route::delete('/{id}/picture', [MemberController::class, 'deletePicture'])->name('picture.delete');
        Route::get('/{id}/picture/info', [MemberController::class, 'getPictureInfo'])->name('picture.info');
        Route::get('/pictures/bulk-upload', function() {
            $members = \App\Models\Member::select('id', 'member_id', 'full_name', 'profile_picture')
                ->orderBy('full_name')
                ->get();
            return view('ceo.members.bulk-picture-upload', compact('members'));
        })->name('pictures.bulk-upload-form');
        Route::post('/pictures/bulk-upload', [MemberController::class, 'bulkUploadPictures'])->name('pictures.bulk-upload');
        
        Route::get('/import/template', [MemberController::class, 'downloadTemplate'])->name('import.template');
        Route::get('/import/form', [MemberController::class, 'importForm'])->name('import');
        Route::post('/import/process', [MemberController::class, 'processImport'])->name('import.process');
    });
    
    // Bio Data
    Route::get('/bio-data/create', [MemberController::class, 'createBioData'])->name('bio-data.create');
    Route::post('/bio-data', [MemberController::class, 'storeBioData'])->name('bio-data.store');
    
    // Financial
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::get('/transactions/{id}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('/transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update');
        Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
        Route::get('/deposits', [TransactionController::class, 'deposits'])->name('deposits');
        Route::get('/withdrawals', [TransactionController::class, 'withdrawals'])->name('withdrawals');
        Route::get('/transfers', [TransactionController::class, 'transfers'])->name('transfers');
        Route::get('/reports', [\App\Http\Controllers\CEO\ReportController::class, 'financial'])->name('reports');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\CEO\ReportController::class, 'index'])->name('index');
        Route::post('/generate', [\App\Http\Controllers\CEO\ReportController::class, 'generate'])->name('generate');
    });
    
    // Loan Applications
    Route::prefix('loan-applications')->name('loan-applications.')->group(function () {
        Route::get('/', [LoanApplicationController::class, 'index'])->name('index');
        Route::get('/create', [LoanApplicationController::class, 'create'])->name('create');
        Route::post('/', [LoanApplicationController::class, 'store'])->name('store');
        Route::get('/{id}', [LoanApplicationController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [LoanApplicationController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LoanApplicationController::class, 'update'])->name('update');
        Route::delete('/{id}', [LoanApplicationController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/approve', [LoanApplicationController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [LoanApplicationController::class, 'reject'])->name('reject');
    });
    
    // Loan Settings
    Route::get('/loan-settings', [LoanSettingsController::class, 'index'])->name('loan-settings');
    Route::put('/loan-settings', [LoanSettingsController::class, 'update'])->name('loan-settings.update');
    
    // Loans
    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/', [LoanController::class, 'index'])->name('index');
        Route::get('/create', [LoanController::class, 'create'])->name('create');
        Route::post('/', [LoanController::class, 'store'])->name('store');
        Route::get('/{id}', [LoanController::class, 'show'])->name('show');
        Route::get('/{id}/print', [LoanController::class, 'printPdf'])->name('print');
        Route::get('/{id}/edit', [LoanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LoanController::class, 'update'])->name('update');
        Route::delete('/{id}', [LoanController::class, 'destroy'])->name('destroy');
        Route::get('/applications', [LoanController::class, 'applications'])->name('applications');
        Route::post('/{id}/approve', [LoanController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [LoanController::class, 'reject'])->name('reject');
        Route::get('/approvals', [LoanController::class, 'approvals'])->name('approvals');
        Route::get('/repayments', [LoanController::class, 'repayments'])->name('repayments');
    });
    
    // Projects
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/create', [ProjectController::class, 'create'])->name('create');
        Route::post('/', [ProjectController::class, 'store'])->name('store');
        Route::get('/{id}', [ProjectController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ProjectController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProjectController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProjectController::class, 'destroy'])->name('destroy');
    });
    
    // Fundraising
    Route::prefix('fundraising')->name('fundraising.')->group(function () {
        Route::get('/', [FundraisingController::class, 'index'])->name('index');
        Route::get('/campaigns', [FundraisingController::class, 'campaigns'])->name('campaigns');
        Route::get('/create', [FundraisingController::class, 'create'])->name('create');
        Route::post('/', [FundraisingController::class, 'store'])->name('store');
        Route::get('/{id}', [FundraisingController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [FundraisingController::class, 'edit'])->name('edit');
        Route::put('/{id}', [FundraisingController::class, 'update'])->name('update');
        Route::delete('/{id}', [FundraisingController::class, 'destroy'])->name('destroy');
    });
    
    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });
    
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/create', [NotificationController::class, 'create'])->name('create');
        Route::post('/', [NotificationController::class, 'store'])->name('store');
        Route::get('/{id}', [NotificationController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [NotificationController::class, 'edit'])->name('edit');
        Route::put('/{id}', [NotificationController::class, 'update'])->name('update');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::get('/history', [NotificationController::class, 'history'])->name('history');
    });
    
    // System
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/settings', [SystemController::class, 'settings'])->name('settings');
        Route::post('/settings', [SystemController::class, 'updateSettings'])->name('settings.update');
        Route::get('/audit-logs', [SystemController::class, 'auditLogs'])->name('audit-logs');
        Route::get('/backups', [SystemController::class, 'backups'])->name('backups');
        Route::post('/backups/create', [SystemController::class, 'createBackup'])->name('backups.create');
        Route::get('/backups/{id}/download', [SystemController::class, 'downloadBackup'])->name('backups.download');
        Route::get('/health', [SystemController::class, 'health'])->name('health');
    });
    
    // System Health
    Route::get('/system-health', [SystemHealthController::class, 'index'])->name('system-health.index');
    
    // Bulk Operations
    Route::view('/bulk-operations', 'ceo.bulk-operations.index')->name('bulk-operations.index');
    
    // Permissions
    Route::view('/permissions', 'ceo.permissions.index')->name('permissions.index');
});
