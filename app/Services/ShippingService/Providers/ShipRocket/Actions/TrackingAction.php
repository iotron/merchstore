<?php

namespace App\Services\ShippingService\Providers\ShipRocket\Actions;

use App\Services\ShippingService\Contracts\Provider\ShippingProviderTrackingContract;
use App\Services\ShippingService\Providers\ShipRocket\ShipRocketApi;
use App\Services\ShippingService\Providers\ShipRocket\Support\hasTrackingStatus;
use GuzzleHttp\Psr7\Utils;

class TrackingAction implements ShippingProviderTrackingContract
{
    use hasTrackingStatus;

    protected ShipRocketApi $api;

    public function __construct(ShipRocketApi $api)
    {
        $this->api = $api;
    }

    public function fetch(int|string $order_id, int|string $channel_id): mixed
    {
        $buildUrl = 'courier/track?order_id='.$order_id;
        if ($channel_id) {
            $buildUrl = $buildUrl.'&channel_id='.$channel_id;
        }

        $response = $this->api->httpGet($buildUrl)->json();

        return $this->updateStatusViaCode($response);
    }

    public function shipment(int|string $shipment_id): mixed
    {
        $response = $this->api->httpGet('courier/track/shipment/'.$shipment_id)->json();

        return $this->updateStatusViaCode($response);
    }

    public function all(array $tracking_ids): mixed
    {
        $response = $this->api->http()
            ->withBody(Utils::streamFor(json_encode(['awbs' => $tracking_ids])))
            ->post($this->api->getBaseUrl().'courier/track/awbs')
            ->json();

        return $response;

    }
}
