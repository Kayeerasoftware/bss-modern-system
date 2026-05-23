<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cashier\DashboardController;
use App\Http\Controllers\Cashier\TransactionController;
use App\Http\Controllers\Cashier\DepositController;
use App\Http\Controllers\Cashier\WithdrawalController;
use App\Http\Controllers\Cashier\MemberController;
use App\Http\Controllers\Cashier\FundraisingController;
use App\Http\Controllers\Cashier\FinancialController;
use App\Http\Controllers\Cashier\LoanController;
use App\Http\Controllers\Cashier\LoanApplicationController;
use App\Http\Controllers\Cashier\LoanSettingsController;
use App\Http\Controllers\Cashier\SavingsController;
use App\Http\Controllers\Cashier\ReportController;

Route::prefix('cashier')->name('cashier.')->middleware(['auth', 'role:cashier,admin'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Cashier\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Cashier\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\Cashier\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/picture', [\App\Http\Controllers\Cashier\ProfileController::class, 'uploadProfilePicture'])->name('profile.picture');
    Route::post('/profile/preferences', [\App\Http\Controllers\Cashier\ProfileController::class, 'updatePreferences'])->name('profile.preferences');
    
    // Transactions
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [TransactionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TransactionController::class, 'update'])->name('update');
        Route::delete('/{id}', [TransactionController::class, 'destroy'])->name('destroy');
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
    
    // Deposits
    Route::prefix('deposits')->name('deposits.')->group(function () {
        Route::get('/', [DepositController::class, 'index'])->name('index');
        Route::get('/create', [DepositController::class, 'create'])->name('create');
        Route::post('/', [DepositController::class, 'store'])->name('store');
        Route::get('/{id}', [DepositController::class, 'show'])->name('show');
    });
    
    // Withdrawals
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [WithdrawalController::class, 'index'])->name('index');
        Route::get('/create', [WithdrawalController::class, 'create'])->name('create');
        Route::post('/', [WithdrawalController::class, 'store'])->name('store');
        Route::get('/{id}', [WithdrawalController::class, 'show'])->name('show');
    });

    // Transfers
    Route::post('/transfers', [TransactionController::class, 'storeTransfer'])->name('transfers.store');
    
    // Members
    Route::prefix('members')->name('members.')->group(function () {
        Route::get('/', [MemberController::class, 'index'])->name('index');
        Route::get('/{id}', [MemberController::class, 'show'])->name('show');
    });
    
    // Loans
    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/applications', [LoanController::class, 'applications'])->name('applications');
        Route::get('/approvals', [LoanController::class, 'approvals'])->name('approvals');
        Route::get('/repayments', [LoanController::class, 'repayments'])->name('repayments');
        Route::get('/', [LoanController::class, 'index'])->name('index');
        Route::get('/create', [LoanController::class, 'create'])->name('create');
        Route::post('/', [LoanController::class, 'store'])->name('store');
        Route::get('/{id}', [LoanController::class, 'show'])->name('show');
        Route::get('/{id}/print', [LoanController::class, 'printPdf'])->name('print');
        Route::get('/{id}/edit', [LoanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LoanController::class, 'update'])->name('update');
        Route::delete('/{id}', [LoanController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/approve', [LoanController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [LoanController::class, 'reject'])->name('reject');
        Route::post('/{id}/disburse', [LoanController::class, 'disburse'])->name('disburse');
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
        Route::get('/{id}/contributions', [FundraisingController::class, 'contributions'])->name('contributions');
        Route::get('/{id}/contributions/create', [FundraisingController::class, 'contributionsCreate'])->name('contributions.create');
        Route::post('/{id}/contributions', [FundraisingController::class, 'contributionsStore'])->name('contributions.store');
        Route::get('/{id}/contributions/{contributionId}', [FundraisingController::class, 'contributionsShow'])->name('contributions.show');
        Route::get('/{id}/contributions/{contributionId}/edit', [FundraisingController::class, 'contributionsEdit'])->name('contributions.edit');
        Route::put('/{id}/contributions/{contributionId}', [FundraisingController::class, 'contributionsUpdate'])->name('contributions.update');
        Route::delete('/{id}/contributions/{contributionId}', [FundraisingController::class, 'contributionsDestroy'])->name('contributions.destroy');
        Route::get('/{id}/contributions/{contributionId}/print', [FundraisingController::class, 'contributionsPrint'])->name('contributions.print');
    });
    
    // Financial
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/', [FinancialController::class, 'index'])->name('index');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/store', [TransactionController::class, 'store'])->name('store');
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
        Route::get('/reports', fn () => redirect()->route('cashier.reports.index'))->name('reports');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [TransactionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TransactionController::class, 'update'])->name('update');
        Route::delete('/{id}', [TransactionController::class, 'destroy'])->name('destroy');
    });

    // Savings
    Route::get('/savings', [SavingsController::class, 'index'])->name('savings.index');
    Route::post('/savings/interest-rate', [SavingsController::class, 'updateInterestRate'])->name('savings.interest-rate');
    
    // Projects
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Cashier\ProjectController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Cashier\ProjectController::class, 'show'])->name('show');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', function () {
            try {
                $summary = [
                    'total_income' => \App\Models\Financial\Transaction::where('type', 'deposit')->sum('amount') ?? 0,
                    'total_expenses' => \App\Models\Financial\Transaction::where('type', 'withdrawal')->sum('amount') ?? 0,
                    'net_balance' => (\App\Models\Financial\Transaction::where('type', 'deposit')->sum('amount') ?? 0) - (\App\Models\Financial\Transaction::where('type', 'withdrawal')->sum('amount') ?? 0),
                    'total_transactions' => \App\Models\Financial\Transaction::count() ?? 0,
                ];
                $reports = \App\Models\Reports\GeneratedReport::where('user_id', auth()->id())->latest()->take(10)->get();
            } catch (\Exception $e) {
                $summary = [
                    'total_income' => 0,
                    'total_expenses' => 0,
                    'net_balance' => 0,
                    'total_transactions' => 0,
                ];
                $reports = collect([]);
            }
            return view('cashier.reports.index', compact('summary', 'reports'));
        })->name('index');
        Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
        Route::get('/{id}', function ($id) {
            $report = \App\Models\Reports\GeneratedReport::where('user_id', auth()->id())->findOrFail($id);
            $controller = new \App\Http\Controllers\Cashier\ReportController();
            $data = $controller->getReportData($report->type, $report->from_date, $report->to_date);
            return view('cashier.reports.view', [
                'type' => $report->type,
                'data' => $data,
                'from_date' => $report->from_date,
                'to_date' => $report->to_date,
                'format' => $report->format,
            ]);
        })->name('view');
        Route::delete('/{id}', function ($id) {
            \App\Models\Reports\GeneratedReport::where('user_id', auth()->id())->findOrFail($id)->delete();
            return redirect()->route('cashier.reports.index')->with('success', 'Report deleted successfully');
        })->name('delete');
    });
    
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Cashier\NotificationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Cashier\NotificationController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Cashier\NotificationController::class, 'store'])->name('store');
        Route::get('/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::post('/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [\App\Http\Controllers\Cashier\NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::delete('/{id}', [\App\Http\Controllers\Cashier\NotificationController::class, 'destroy'])->name('destroy');
        Route::get('/{id}', [\App\Http\Controllers\Cashier\NotificationController::class, 'show'])->name('show');
    });
    
    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Cashier\UsersController::class, 'index'])->name('index');
    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::view('/', 'cashier.settings.index')->name('index');
    });
});
