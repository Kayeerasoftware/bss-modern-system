<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\LoanController;
use App\Http\Controllers\API\MemberController;
use App\Http\Controllers\API\ClientDashboardController;
use App\Http\Controllers\CompleteDashboardController;
use App\Http\Controllers\ProjectController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Dashboard Data
Route::get('/dashboard-data', [CompleteDashboardController::class, 'getDashboardData']);
Route::get('/ceo-data', [DashboardController::class, 'getCeoData']);

// Loan Management
Route::get('/loans', [LoanController::class, 'index']);
Route::post('/loans', [LoanController::class, 'store']);
Route::post('/loans/{id}/approve', [LoanController::class, 'approve']);
Route::post('/loans/{id}/reject', [LoanController::class, 'reject']);
Route::put('/loans/{id}', [LoanController::class, 'update']);
Route::delete('/loans/{id}', [LoanController::class, 'destroy']);

// Member Management
Route::get('/members', [MemberController::class, 'index']);
Route::post('/members', [MemberController::class, 'store']);
Route::put('/members/{id}', [MemberController::class, 'update']);
Route::post('/members/{id}/picture', [MemberController::class, 'uploadPicture']);
Route::delete('/members/{id}', [MemberController::class, 'destroy']);

// Project Management
Route::get('/projects', [ProjectController::class, 'index']);
Route::post('/projects', [ProjectController::class, 'store']);
Route::put('/projects/{id}', [ProjectController::class, 'update']);
Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);

// Client Dashboard
Route::get('/client-dashboard', [ClientDashboardController::class, 'getClientData']);
Route::post('/client-dashboard/deposit', [ClientDashboardController::class, 'makeDeposit']);
Route::get('/client-dashboard/savings-history', [ClientDashboardController::class, 'getSavingsHistory']);
Route::get('/client-dashboard/transaction-distribution', [ClientDashboardController::class, 'getTransactionDistribution']);
Route::get('/client-dashboard/spending-categories', [ClientDashboardController::class, 'getSpendingCategories']);

// Reports
Route::post('/reports/generate', [DashboardController::class, 'generateReport']);
Route::get('/reports/status', [DashboardController::class, 'getReportStatus']);
