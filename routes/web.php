<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReceiptController;

Route::get('/dues-transactions/{transaction}/receipt', [ReceiptController::class, 'download'])
    ->middleware('auth')
    ->name('dues.receipt.download');

    Route::get(
        '/transactions/{transaction}/expense-receipt/download',
        [ReceiptController::class, 'downloadExpense']
    )->name('expense.receipt.download');