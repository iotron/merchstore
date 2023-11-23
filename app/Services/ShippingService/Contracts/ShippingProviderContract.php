<?php

namespace App\Services\ShippingService\Contracts;

use App\Services\ShippingService\Contracts\Provider\ShippingProviderActionContract;

interface ShippingProviderContract
{

    public function getProviderName(): string;
    public function getClass():string;

    public function setError(string $error):void;
    public function getError():?string;

    public function order():ShippingProviderActionContract;
    public function courier();
    public function return();
    public function shipment();
    public function tracking();

}
