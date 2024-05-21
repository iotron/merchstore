<?php

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:customer'])->group(function () {
    // POS Route
    Route::get('remove-cart/{customer}/{product}', function (\App\Models\Customer\Customer $customer, \App\Models\Product\Product $product) {
        if ($customer->cart->contains('id', $product->id)) {
            if ($customer->cart()->detach($product->id)) {
                Notification::make()
                    ->title($product->sku.' Remove successfully')
                    ->send();
            }
        }

        return back();
    })->name('remove-cart');

    // Checkout Route
    Route::get('checkout/{payment:receipt}', \App\Livewire\CheckoutPage::class)->name('payment.visit');

});

Route::get('test', [\App\Http\Controllers\TestController::class, 'index']);
