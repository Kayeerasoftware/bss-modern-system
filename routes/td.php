<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TD\DashboardController;
use App\Http\Controllers\TD\ProjectController;
use App\Http\Controllers\TD\MemberController;
use App\Http\Controllers\TD\LoanController;
use App\Http\Controllers\TD\ReportController;
use App\Http\Controllers\TD\ProfileController;
use App\Http\Controllers\TD\PhotoController;

Route::prefix('td')->name('td.')->middleware(['auth', 'role:td,admin'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Projects
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/{id}', [ProjectController::class, 'show'])->name('show');
        Route::put('/{id}/progress', [ProjectController::class, 'updateProgress'])->name('progress');
    });
    
    // Members
    Route::prefix('members')->name('members.')->group(function () {
        Route::get('/', [MemberController::class, 'index'])->name('index');
        Route::get('/{id}', [MemberController::class, 'show'])->name('show');
    });
    
    // Loans
    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/', [LoanController::class, 'index'])->name('index');
        Route::get('/{id}', [LoanController::class, 'show'])->name('show');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/projects', [ReportController::class, 'projects'])->name('projects');
        Route::get('/members', [ReportController::class, 'members'])->name('members');
        Route::get('/loans', [ReportController::class, 'loans'])->name('loans');
    });
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/picture', [ProfileController::class, 'uploadProfilePicture'])->name('profile.picture');
    
    // Photos
    Route::prefix('photos')->name('photos.')->group(function () {
        Route::get('/', [PhotoController::class, 'index'])->name('index');
        Route::post('/', [PhotoController::class, 'store'])->name('store');
        Route::put('/{id}', [PhotoController::class, 'update'])->name('update');
        Route::delete('/{id}', [PhotoController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle', [PhotoController::class, 'toggleStatus'])->name('toggle');
    });
});
