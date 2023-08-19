<?php

use Illuminate\Support\Facades\Route;



Route::get('/',[\App\Http\Controllers\Api\ProductController::class,'index']);
Route::get('{product:url}',[\App\Http\Controllers\Api\ProductController::class,'show']);

