<?php

namespace App\Services\ShippingService\Contracts\Provider;

use App\Models\Order\OrderProduct;
use App\Models\Order\OrderShipment;

interface ShippingProviderReturnContract
{
    public function create(OrderProduct $orderProduct, OrderShipment $orderShipment): mixed;
}
