<?php

namespace App\Services\ShippingService\Contracts\Provider;

use App\Models\Order\OrderShipment;

interface ShippingProviderActionContract
{
    public function create(OrderShipment $orderShipment);

    public function all();

    public function fetch(int|string $id);

    public function verify(): bool;
}
