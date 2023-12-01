<?php

namespace App\Services\OrderService;

use App\Models\Order\Order;
use App\Models\Order\OrderShipment;
use App\Models\Shipping\ShippingProvider;
use App\Services\OrderService\Shipping\OrderShipmentShippingService;
use App\Services\ShippingService\ShippingService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class OrderShippedService
{


    private Order|Model $order;
    protected ?string $error = null;
    private ShippingService $shippingService;

    public function __construct(Order|Model $order, ShippingService $shippingService)
    {
        $this->order = $order;
        $this->order->loadMissing('shipments','shipments.shippingProvider');
        $this->shippingService = $shippingService;
    }

    public function send():bool
    {
        foreach ($this->order->shipments as $shipment)
        {
            if (is_null($this->error))
            {
                $shippingProvider = $shipment->shippingProvider->code;
                $shippingServiceProvider = $this->shippingService->provider($shippingProvider)->getProvider();

                $orderShipmentShippingService = new OrderShipmentShippingService($shipment,$shippingServiceProvider);
                if (!$orderShipmentShippingService->shipped())
                {
                    $this->error = $orderShipmentShippingService->getError();
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
