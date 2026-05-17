<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionImportController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/transactions', [DashboardController::class, 'transactions'])->name('transactions');
    Route::get('/assets', [DashboardController::class, 'assets'])->name('assets');
    Route::get('/profit-loss', [DashboardController::class, 'profitLoss'])->name('profit-loss');
});

Route::get('/transactions/import', [TransactionImportController::class, 'index'])->name('transactions.index');
Route::post('/transactions/import', [TransactionImportController::class, 'import'])->name('transactions.import');
