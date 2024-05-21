<?php

namespace App\Services\ShippingService\Providers\Custom;

use App\Models\Shipping\ShippingProvider;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderActionContract;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderCourierContract;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderReturnContract;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderTrackingContract;
use App\Services\ShippingService\Contracts\ShippingProviderContract;
use App\Services\ShippingService\Providers\Custom\Actions\CourierAction;
use App\Services\ShippingService\Providers\Custom\Actions\OrderAction;
use App\Services\ShippingService\Providers\Custom\Actions\ReturnAction;
use App\Services\ShippingService\Providers\Custom\Actions\TrackingAction;

class CustomShippingService implements ShippingProviderContract
{
    private ?string $error = null;

    protected ShippingProvider $providerModel;

    public function __construct(ShippingProvider $providerModel, mixed $api = null)
    {
        $this->providerModel = $providerModel;
    }

    public function getProviderName(): string
    {
        return 'custom';
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
        return new OrderAction();
    }

    public function courier(): ShippingProviderCourierContract
    {
        return new CourierAction();
    }

    public function return(): ShippingProviderReturnContract
    {
        return new ReturnAction();
    }

    public function shipment()
    {
        // TODO: Implement shipment() method.
    }

    public function tracking(): ShippingProviderTrackingContract
    {
        return new TrackingAction();
    }
}
