<?php

namespace App\Services\ShippingService\Contracts;

interface ShippingProviderContract
{

    public function order();
    public function courier();
    public function return();
    public function shipment();
    public function tracking();

}
