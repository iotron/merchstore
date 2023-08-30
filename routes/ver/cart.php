<?php

use App\Http\Controllers\Api\CartController;
use Illuminate\Support\Facades\Route;


Route::resource('cart', CartController::class, [
    'parameters' => [
        'cart' => 'products'
    ]
]);

Route::post('cart/add/{product}',[CartController::class,'add']);
Route::post('cart/coupon/apply', [CartController::class, 'applyCouponCode']);
Route::delete('cart/coupon/delete', [CartController::class, 'removeCouponCode']);
