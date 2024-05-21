<?php

namespace App\Services\ShippingService\Providers\Custom\Actions;

use App\Models\Order\OrderShipment;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderActionContract;
use App\Services\ShippingService\Providers\Custom\Support\OrderHandler;
use App\Services\ShippingService\Providers\ShipRocket\Support\hasShippableOrders;

class OrderAction implements ShippingProviderActionContract
{
    use hasShippableOrders;

    public function create(OrderShipment $orderShipment)
    {

        $added = [
            //            "order_id" => random_int(11111111,99999999),
            'shipment_id' => random_int(11111111, 99999999),
            'status' => 'NEW',
            'status_code' => 1,
            'onboarding_completed_now' => 0,
            'awb_code' => null,
            'courier_company_id' => null,
            'courier_name' => null,
        ];
        $result = array_merge($added, $this->format($orderShipment));
        $result['order_id'] = random_int(11111111, 99999999);
        $result['payment_method'] = 'COD';

        return $result;
    }

    public function all()
    {
        // TODO: Implement all() method.
    }

    public function fetch(int|string $id)
    {
        return OrderHandler::getOrderDetailArray($id);
    }

    public function verify(): bool
    {
        // TODO: Implement verify() method.
    }
}
