<?php

namespace App\Services\ShippingService\Providers\Custom;

use App\Services\ShippingService\Contracts\Provider\ShippingProviderActionContract;
use App\Services\ShippingService\Contracts\ShippingProviderContract;
use App\Services\ShippingService\Providers\Custom\Actions\OrderAction;

class CustomShippingService implements ShippingProviderContract
{

    private ?string $error = null;

    public function getProviderName(): string
    {
        return 'custom';
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
        return new OrderAction();
    }

    public function courier()
    {
        // TODO: Implement courier() method.
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
