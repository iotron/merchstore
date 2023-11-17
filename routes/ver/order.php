<?php

use App\Http\Controllers\Api\Order\OrderActionController;
use Illuminate\Support\Facades\Route;


Route::prefix('order')->group(function (){

    Route::post('place',[OrderActionController::class,'placeOrder']);



});
