<?php

namespace App\Services\ShippingService\Providers\ShipRocket;

use App\Models\Shipping\ShippingProvider;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderActionContract;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderCourierContract;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderReturnContract;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderTrackingContract;
use App\Services\ShippingService\Contracts\ShippingProviderContract;
use App\Services\ShippingService\Providers\ShipRocket\Actions\CourierAction;
use App\Services\ShippingService\Providers\ShipRocket\Actions\OrderAction;
use App\Services\ShippingService\Providers\ShipRocket\Actions\ReturnAction;
use App\Services\ShippingService\Providers\ShipRocket\Actions\TrackingAction;

class ShipRocketShippingService implements ShippingProviderContract
{
    protected ShipRocketApi $api;

    private ?string $error = null;

    protected ShippingProvider $providerModel;

    public function __construct(ShippingProvider $providerModel, ShipRocketApi $api)
    {
        $this->providerModel = $providerModel;
        $this->api = $api;
    }

    public function getProviderName(): string
    {
        return 'shiprocket';
    }

    public function getClass(): string
    {
        return get_class($this);
    }

    public function getProvider(): ShippingProviderContract
    {
        return $this;
    }

    public function getModel(): ShippingProvider
    {
        return $this->providerModel;
    }

    public function setError(string $error): void
    {
        $this->error = $error;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function order(): ShippingProviderActionContract
    {
        return new OrderAction($this->api);
    }

    public function courier(): ShippingProviderCourierContract
    {
        return new CourierAction($this->api);
    }

    public function return(): ShippingProviderReturnContract
    {
        return new ReturnAction($this->api);
    }

    public function shipment()
    {
        // TODO: Implement shipment() method.
    }

    public function tracking(): ShippingProviderTrackingContract
    {
        return new TrackingAction($this->api);
    }
}
