<?php

namespace App\Services\ShippingService\Providers\ShipRocket\Actions;

use App\Services\ShippingService\Contracts\Provider\ShippingProviderTrackingContract;
use App\Services\ShippingService\Providers\ShipRocket\ShipRocketApi;

class TrackingAction implements ShippingProviderTrackingContract
{

    protected ShipRocketApi $api;

    public function __construct(ShipRocketApi $api)
    {
        $this->api = $api;
    }


    /**
     * @param int|string $order_id
     * @param int|string $channel_id
     * @return string|object|null
     */
    public function fetch(int|string $order_id, int|string $channel_id): string|object|null
    {
        $buildUrl = $this->api->getBaseUrl().'courier/track?order_id='.$order_id;
        if ($channel_id)
        {
            $buildUrl = $buildUrl. '&channel_id='.$channel_id;
        }

        $response = $this->api->http()
            ->withHeaders(['application/json'])
            ->get($buildUrl);
        return
    }

    /**
     * @param int|string $shipment_id
     * @return string|object|null
     */
    public function shipment(int|string $shipment_id): string|object|null
    {
        // TODO: Implement shipment() method.
    }
}
