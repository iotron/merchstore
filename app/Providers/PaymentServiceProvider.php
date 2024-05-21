<?php

namespace App\Providers;

use App\Models\Payment\PaymentProvider;
use App\Services\PaymentService\Contracts\PaymentServiceContract;
use App\Services\PaymentService\PaymentService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        // For Service Class
        $this->app->singleton(PaymentService::class, function () {
            // PaymentService::activateProviders('razorpay','stripe');
            $newPaymentService = (app()->isProduction()) ? new PaymentService(PaymentProvider::RAZORPAY) : new PaymentService(PaymentProvider::RAZORPAY, PaymentProvider::CUSTOM);
            throw_unless($newPaymentService instanceof PaymentServiceContract, get_class($newPaymentService).' must implement App\Services\PaymentService\Contracts\PaymentServiceContract');

            return $newPaymentService;
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
