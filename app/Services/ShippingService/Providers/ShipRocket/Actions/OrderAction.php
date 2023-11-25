<?php

namespace App\Services\ShippingService\Providers\ShipRocket\Actions;

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
    public function create(array $data)
    {
        $response = $this->api->http()
            ->withBody([],'application/json')
            ->post($this->api->getBaseUrl().'orders/create/adhoc')
            ->throw()
            ->json();
        return $response->body();

    }

    public function all(): string
    {
        $response = $this->api->http()->get($this->api->getBaseUrl().'orders');
        return $response->body();
    }

    public function fetch(int|string $id): string
    {
        $response = $this->api->http()->get($this->api->getBaseUrl().'orders/show/'.$id);
        return $response->body();
    }

    public function verify(): bool
    {
        // TODO: Implement verify() method.
    }
}
