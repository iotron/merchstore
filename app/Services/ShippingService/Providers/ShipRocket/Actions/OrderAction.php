<?php

namespace App\Services\ShippingService\Providers\ShipRocket\Actions;

use App\Models\Order\Order;
use App\Models\Order\OrderShipment;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderActionContract;
use App\Services\ShippingService\Providers\ShipRocket\ShipRocketApi;
use App\Services\ShippingService\Providers\ShipRocket\Support\hasShippableOrders;
use Illuminate\Http\Client\RequestException;

class OrderAction implements ShippingProviderActionContract
{
    use hasShippableOrders;

    protected ShipRocketApi $api;

    public function __construct(ShipRocketApi $api)
    {
        $this->api = $api;
    }

    /**
     * @throws RequestException
     */
    public function create(OrderShipment $orderShipment)
    {
        $response = $this->api->httpPost('orders/create/adhoc',$this->format($orderShipment))
            ->throw()
            ->json();
        return $response;
    }

    public function all()
    {
        $response = $this->api->http()->get($this->api->getBaseUrl().'orders');
        return $response->json();
    }

    public function fetch(int|string $id)
    {
        $response = $this->api->http()->get($this->api->getBaseUrl().'orders/show/'.$id);
        return $response->body();
    }

    public function verify(): bool
    {
        // TODO: Implement verify() method.
    }
}
