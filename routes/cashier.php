<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cashier\DashboardController;
use App\Http\Controllers\Cashier\TransactionController;
use App\Http\Controllers\Cashier\DepositController;
use App\Http\Controllers\Cashier\WithdrawalController;
use App\Http\Controllers\Cashier\MemberController;

Route::prefix('cashier')->name('cashier.')->middleware(['auth', 'role:cashier,admin'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');
    
    // Transactions
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('show');
    });
    
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
    
    // Members
    Route::prefix('members')->name('members.')->group(function () {
        Route::get('/', [MemberController::class, 'index'])->name('index');
        Route::get('/{id}', [MemberController::class, 'show'])->name('show');
    });
    
    // Loans
    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Cashier\LoanController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Cashier\LoanController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Cashier\LoanController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Cashier\LoanController::class, 'show'])->name('show');
    });
    
    // Fundraising
    Route::prefix('fundraising')->name('fundraising.')->group(function () {
        Route::view('/', 'cashier.fundraising.index')->name('index');
    });
    
    // Financial
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Cashier\FinancialController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Cashier\FinancialController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Cashier\FinancialController::class, 'store'])->name('store');
        Route::get('/deposits', [\App\Http\Controllers\Cashier\FinancialController::class, 'deposits'])->name('deposits');
        Route::get('/withdrawals', [\App\Http\Controllers\Cashier\FinancialController::class, 'withdrawals'])->name('withdrawals');
        Route::get('/transfers', [\App\Http\Controllers\Cashier\FinancialController::class, 'transfers'])->name('transfers');
    });
    
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
        Route::post('/generate', [\App\Http\Controllers\Admin\ReportController::class, 'generate'])->name('generate');
        Route::get('/{id}', function ($id) {
            $report = \App\Models\Reports\GeneratedReport::where('user_id', auth()->id())->findOrFail($id);
            $controller = new \App\Http\Controllers\Admin\ReportController();
            $data = $controller->getReportData($report->type, $report->from_date, $report->to_date);
            return view('admin.reports.view', [
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
    
    // Profile
    Route::get('/profile', [\App\Http\Controllers\Cashier\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Cashier\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\Cashier\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/picture', [\App\Http\Controllers\Cashier\ProfileController::class, 'uploadProfilePicture'])->name('profile.picture');
    Route::post('/profile/preferences', [\App\Http\Controllers\Cashier\ProfileController::class, 'updatePreferences'])->name('profile.preferences');
});
