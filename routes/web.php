<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionImportController;

Route::get('/', [TransactionImportController::class, 'index'])->name('transactions.index');
Route::post('/transactions/import', [TransactionImportController::class, 'import'])->name('transactions.import');
