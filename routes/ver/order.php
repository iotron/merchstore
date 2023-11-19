<?php

use App\Http\Controllers\Api\Order\OrderActionController;
use App\Http\Controllers\Api\Order\OrderController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth:customer'])->group(function(){
    Route::post('place',[OrderActionController::class,'placeOrder']);

    Route::get('all',[OrderController::class,'index']);
});



Route::match(['get','post'],'confirm-payment/{payment:receipt}',[OrderActionController::class,'confirmPayment'])->name('confirm.payment');
