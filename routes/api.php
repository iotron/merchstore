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

Route::post('capture/payment', [\App\Http\Controllers\Api\Order\OrderActionController::class, 'captureCallback'])->name('payment.capture');
//
//// Auth
Route::prefix('/')->group(base_path('routes/v1/auth/customer.php'));

Route::prefix('products')->group(base_path('routes/v1/products.php'));
Route::prefix('categories')->group(base_path('routes/v1/categories.php'));
Route::prefix('themes')->group(base_path('routes/v1/themes.php'));

// Cart
Route::middleware(['auth:customer'])->group(base_path('routes/v1/cart.php'));
// Customer Address
Route::middleware(['auth:customer'])->group(base_path('routes/v1/address.php'));

//Order
Route::prefix('order')->group(base_path('routes/v1/order.php'));
