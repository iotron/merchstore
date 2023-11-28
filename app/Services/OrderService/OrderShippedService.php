<?php

namespace App\Services\OrderService;

use App\Models\Order\Order;
use App\Services\ShippingService\ShippingService;

class OrderShippedService
{


    private Order $order;
    protected ?string $error = null;
    private ShippingService $shippingService;

    public function __construct(Order $order, ShippingService $shippingService)
    {
        $order->load('shipments');
        $this->order = $order;
        $this->shippingService = $shippingService;
    }

    public function send():bool
    {
        foreach ($this->order->shipments as $shipment)
        {
            if (empty($this->error))
            {
                $newOrderOnProvider = $this->shippingService->provider('shiprocket')->order()->create($shipment);
                if (isset($newOrderOnProvider['message']))
                {
                    // has error
                    // Pickup location need to added.. in shiprocket.. Wrong Pickup location entered. Please choose one location from the data given
                    $this->error = $newOrderOnProvider['message'];
                }
            }

        }
        return is_null($this->error);
    }

    public function getError(): ?string
    {
        return $this->error;
    }

}
