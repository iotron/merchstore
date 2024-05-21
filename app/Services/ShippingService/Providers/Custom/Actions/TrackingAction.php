<?php

namespace App\Services\ShippingService\Providers\Custom\Actions;

use App\Models\Order\OrderShipment;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderTrackingContract;
use Illuminate\Support\Str;

class TrackingAction implements ShippingProviderTrackingContract
{
    public function fetch(int|string $order_id, int|string $channel_id): string|object|null
    {
        return OrderShipment::with('order', 'orderProducts', 'pickupAddress', 'deliveryAddress', 'shippingProvider')
            ->firstWhere('order_id', $order_id);
    }

    public function shipment(int|string $shipment_id): mixed
    {
        //        return OrderShipment::with('order','orderProducts','pickupAddress','deliveryAddress','shippingProvider')
        //            ->firstWhere('tracking_id',$shipment_id);

        return [
            [
                'tracking_data' => [
                    'track_status' => true,
                    'shipment_status' => true,
                    'shipment_track' => [
                        [
                            'id' => Str::random(8),
                            'awb_code' => Str::random(8),
                        ],
                    ],
                    'shipment_track_activities' => [
                        [
                            'date' => now()->format('Y-m-d H:i:s'),
                            'status' => 'Custom Status',
                            'activity' => 'Custom Activity',
                            'location' => 'Custom Location',
                            'sr-status' => null,
                        ],
                    ],
                    'etd' => now()->format('Y-m-d H:i:s'),
                ],
            ],
        ];

    }

    /**
     * @return mixed
     */
    public function all(array $tracking_ids)
    {
        // TODO: Implement all() method.
    }
}
