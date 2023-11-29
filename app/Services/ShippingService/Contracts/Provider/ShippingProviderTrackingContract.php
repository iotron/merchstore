<?php

namespace App\Services\ShippingService\Contracts\Provider;

interface ShippingProviderTrackingContract
{


    public function fetch(int|string $order_id,int|string $channel_id): mixed;
    public function shipment(int|string $shipment_id): mixed;

    public function all(array $tracking_ids);

}
