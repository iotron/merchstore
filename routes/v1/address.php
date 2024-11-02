<?php

use App\Http\Controllers\Api\AddressController;
use Illuminate\Support\Facades\Route;

Route::prefix('address')->group(function () {

    Route::get('/', [AddressController::class, 'index']);
    Route::get('{address:id}', [AddressController::class, 'show']);
    Route::post('create', [AddressController::class, 'create']);
    Route::post('update/{address:id}', [AddressController::class, 'update']);
    Route::post('delete/{address:id}', [AddressController::class, 'destroy']);

});
