<?php

namespace App\Services\ShippingService\Contracts\Provider;

interface ShippingProviderTrackingContract
{


    public function fetch(int|string $order_id,int|string $channel_id): string|object|null;
    public function shipment(int|string $shipment_id): string|object|null;


}
