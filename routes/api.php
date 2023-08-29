<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth
Route::prefix('/')->group(base_path('routes/ver/auth/customer.php'));

Route::prefix('products')->group(base_path('routes/ver/products.php'));
Route::prefix('categories')->group(base_path('routes/ver/categories.php'));

// Cart
Route::middleware(['auth:customer'])->group(base_path('routes/ver/cart.php'));
