<?php

namespace App\Providers;

use App\Services\Iotron\LaravelRazorpay\LaravelRazorpay;
use Illuminate\Support\ServiceProvider;


class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        $this->app->singleton(LaravelRazorpay::class, function ($app) {
            return new LaravelRazorpay();
        });

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
