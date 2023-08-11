<?php

use Illuminate\Support\Facades\Route;



Route::resource('/',\App\Http\Controllers\Api\ProductController::class);

