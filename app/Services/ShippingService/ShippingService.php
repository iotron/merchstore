<?php

namespace App\Services\ShippingService;

use App\Services\ShippingService\Contracts\ShippingServiceContract;

class ShippingService implements ShippingServiceContract
{


    public function __construct(...$provider_name)
    {
        // Enable All Available Payment Providers
        $this->activateProviders($provider_name);
    }

    private function activateProviders(array $provider_name)
    {
    }


}
