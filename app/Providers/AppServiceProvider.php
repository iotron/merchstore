<?php

namespace App\Providers;

use App\Helpers\Cart\Cart;
use App\Models\Order\OrderShipment;
use App\Observers\Order\ShipmentTrackActivityObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Cart::class, function ($app) {

            return new Cart(auth('customer')->user(), session('coupon'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
