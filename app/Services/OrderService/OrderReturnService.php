<?php

namespace App\Services\OrderService;

use App\Models\Order\Order;
use App\Services\ShippingService\ShippingService;
use Illuminate\Database\Eloquent\Model;

class OrderReturnService
{

    private Order|Model $order;
    protected ?string $error = null;
    private ShippingService $shippingService;

    public function __construct(Order|Model $order, ShippingService $shippingService)
    {
        $order->load('orderProducts','orderProducts.product','orderProducts.shipment','shipments','shipments.shippingProvider');
        $this->order = $order;
        $this->shippingService = $shippingService;

    }

    public function getError(): ?string
    {
        return $this->error;
    }


    public function return():bool
    {
        foreach ($this->order->orderProducts as $orderProduct)
        {
            if ($orderProduct->product->is_returnable)
            {
                // Returnable Item Found
//                $this->shippingService->provider($this->order->shipments->first()->shippingProvider->code)->return()->create($orderProduct);

                    foreach ($orderProduct->shipment as $orderShipment)
                    {
                        $this->shippingService->provider('shiprocket')->return()->create($orderProduct,$orderShipment);
                    }


            }
        }
        return is_null($this->error);
    }





}
