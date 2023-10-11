<?php

use Illuminate\Support\Facades\Route;



Route::get('/',[\App\Http\Controllers\Api\ProductController::class,'index']);
Route::get('{product:url}',[\App\Http\Controllers\Api\ProductController::class,'show']);
Route::get('{category:url}/category',[\App\Http\Controllers\Api\ProductController::class,'showProductsByCategory']);
Route::get('{theme:url}/theme',[\App\Http\Controllers\Api\ProductController::class,'showProductsByTheme']);
