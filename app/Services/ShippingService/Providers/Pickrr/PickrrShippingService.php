<?php

namespace App\Services\ShippingService\Providers\Pickrr;

use App\Models\Shipping\ShippingProvider;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderActionContract;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderCourierContract;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderReturnContract;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderTrackingContract;
use App\Services\ShippingService\Contracts\ShippingProviderContract;

class PickrrShippingService implements ShippingProviderContract
{
    private ?string $error = null;
    protected ShippingProvider $providerModel;

    public function __construct(ShippingProvider $providerModel,mixed $api = null)
    {
        $this->providerModel = $providerModel;
    }



    /**
     * @return string
     */
    public function getProviderName(): string
    {
        // TODO: Implement getProviderName() method.
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        // TODO: Implement getClass() method.
    }

    /**
     * @return ShippingProviderContract
     */
    public function getProvider(): ShippingProviderContract
    {
        return $this;
    }


    public function getModel(): ShippingProvider
    {
        return $this->providerModel;
    }


    /**
     * @param string $error
     * @return void
     */
    public function setError(string $error): void
    {
        // TODO: Implement setError() method.
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        // TODO: Implement getError() method.
    }

    /**
     * @return ShippingProviderActionContract
     */
    public function order(): ShippingProviderActionContract
    {
        // TODO: Implement order() method.
    }

    /**
     * @return ShippingProviderCourierContract
     */
    public function courier(): ShippingProviderCourierContract
    {
        // TODO: Implement courier() method.
    }

    /**
     * @return ShippingProviderReturnContract
     */
    public function return(): ShippingProviderReturnContract
    {
        // TODO: Implement return() method.
    }

    /**
     * @return mixed
     */
    public function shipment()
    {
        // TODO: Implement shipment() method.
    }

    /**
     * @return ShippingProviderTrackingContract
     */
    public function tracking(): ShippingProviderTrackingContract
    {
        // TODO: Implement tracking() method.
    }
}
