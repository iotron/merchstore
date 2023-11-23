<?php

namespace App\Providers;


use App\Services\ShippingService\Contracts\ShippingServiceContract;
use App\Services\ShippingService\ShippingService;
use Illuminate\Support\ServiceProvider;

class ShippingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ShippingService::class,function (){
            $newShippingService = (app()->isProduction()) ? new ShippingService('custom') : new ShippingService('custom','shiprocket');
            throw_unless($newShippingService instanceof ShippingServiceContract, get_class($newShippingService) . ' must implement App\Services\ShippingService\Contracts\ShippingServiceContract');
            return $newShippingService;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
