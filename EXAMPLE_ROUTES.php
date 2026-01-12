<?php

// Example routes using permission middleware

// Member Management Routes
Route::middleware(['auth', 'permission:view_members'])->group(function () {
    Route::get('/members', [MemberController::class, 'index']);
    Route::get('/members/{id}', [MemberController::class, 'show']);
});

Route::post('/members', [MemberController::class, 'store'])
    ->middleware(['auth', 'permission:create_members']);

Route::put('/members/{id}', [MemberController::class, 'update'])
    ->middleware(['auth', 'permission:edit_members']);

Route::delete('/members/{id}', [MemberController::class, 'destroy'])
    ->middleware(['auth', 'permission:delete_members']);

// Loan Management Routes
Route::middleware(['auth', 'permission:view_loans'])->group(function () {
    Route::get('/loans', [LoanController::class, 'index']);
});

Route::post('/loans/{id}/approve', [LoanController::class, 'approve'])
    ->middleware(['auth', 'permission:approve_loans']);

// Transaction Routes
Route::get('/transactions', [TransactionController::class, 'index'])
    ->middleware(['auth', 'permission:view_transactions']);

Route::post('/transactions', [TransactionController::class, 'store'])
    ->middleware(['auth', 'permission:create_transactions']);

// Project Routes
Route::middleware(['auth', 'permission:view_projects'])->group(function () {
    Route::get('/projects', [ProjectController::class, 'index']);
});

Route::post('/projects', [ProjectController::class, 'store'])
    ->middleware(['auth', 'permission:create_projects']);

// Reports Routes
Route::get('/reports', [ReportController::class, 'index'])
    ->middleware(['auth', 'permission:view_reports']);

Route::post('/reports/generate', [ReportController::class, 'generate'])
    ->middleware(['auth', 'permission:generate_reports']);

// Settings Routes
Route::get('/settings', [SettingsController::class, 'index'])
    ->middleware(['auth', 'permission:view_settings']);

Route::post('/settings', [SettingsController::class, 'update'])
    ->middleware(['auth', 'permission:edit_settings']);

// Admin Routes
Route::middleware(['auth', 'permission:manage_users'])->group(function () {
    Route::get('/admin/users', [AdminController::class, 'users']);
    Route::post('/admin/users', [AdminController::class, 'createUser']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);
});

Route::get('/admin/permissions', [PermissionController::class, 'index'])
    ->middleware(['auth', 'permission:manage_permissions']);
