<?php

namespace App\Services\ShippingService\Providers\Custom\Actions;

use App\Models\Order\OrderShipment;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderTrackingContract;

class TrackingAction implements ShippingProviderTrackingContract
{

    /**
     * @param int|string $order_id
     * @param int|string $channel_id
     * @return string|object|null
     */
    public function fetch(int|string $order_id, int|string $channel_id): string|object|null
    {
        return OrderShipment::with('order','orderProducts','pickupAddress','deliveryAddress','shippingProvider')
            ->firstWhere('order_id',$order_id);
    }

    /**
     * @param int|string $shipment_id
     * @return string|object|null
     */
    public function shipment(int|string $shipment_id): string|object|null
    {
        return OrderShipment::with('order','orderProducts','pickupAddress','deliveryAddress','shippingProvider')
            ->firstWhere('tracking_id',$shipment_id);
    }
}
