<?php

namespace App\Services\ShippingService\Providers\ShipRocket;

use App\Services\ShippingService\Contracts\Provider\ShippingProviderActionContract;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderCourierContract;
use App\Services\ShippingService\Contracts\ShippingProviderContract;
use App\Services\ShippingService\Providers\ShipRocket\Actions\CourierAction;
use App\Services\ShippingService\Providers\ShipRocket\Actions\OrderAction;

class ShipRocketShippingService implements ShippingProviderContract
{


    protected ShipRocketApi $api;
    private ?string $error = null;

    public function __construct(ShipRocketApi $api)
    {
        $this->api = $api;
    }


    public function getProviderName(): string
    {
        return 'shiprocket';
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return get_class($this);
    }

    /**
     * @param string $error
     * @return void
     */
    public function setError(string $error): void
    {
        $this->error = $error;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }


    public function order():ShippingProviderActionContract
    {
        return new OrderAction($this->api);
    }

    public function courier():ShippingProviderCourierContract
    {
        return new CourierAction($this->api);
    }

    public function return()
    {
        // TODO: Implement return() method.
    }

    public function shipment()
    {
        // TODO: Implement shipment() method.
    }

    public function tracking()
    {
        // TODO: Implement tracking() method.
    }


}
