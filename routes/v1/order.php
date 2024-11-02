<?php

use App\Http\Controllers\Api\Order\OrderActionController;
use App\Http\Controllers\Api\Order\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:customer'])->group(function () {
    Route::post('place', [OrderActionController::class, 'placeOrder']);

    Route::get('/', [OrderController::class, 'index']);
    Route::get('{order:uuid}', [OrderController::class, 'show']);
    Route::get('{order:uuid}/invoice', [OrderController::class, 'viewInvoice']);

    Route::post('{order:uuid}/return', [OrderActionController::class, 'returnOrder']);

});

Route::match(['get', 'post'], 'confirm-payment/{payment:receipt}', [OrderActionController::class, 'confirmPayment'])->name('confirm.payment');
