<?php

namespace App\Services\OrderService\Return;

use App\Models\Order\OrderProduct;
use App\Models\Order\OrderShipment;
use App\Models\Shipping\ShippingProvider;
use App\Services\ShippingService\Contracts\ShippingProviderContract;
use App\Services\ShippingService\Contracts\ShippingServiceContract;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class OrderProductReturnService
{


    protected ShippingServiceContract $shippingService;
    protected OrderProduct|Model $orderProduct;
    protected ShippingProvider $shippingProviderModel;
    protected ShippingProviderContract $shippingProvider;
    private ?string $error = null;

    public function __construct(ShippingServiceContract $shippingService, OrderProduct|Model $orderProduct)
    {
        $this->shippingService = $shippingService;
        $orderProduct->loadMissing('order','order.customer','shipment','shipment.pickupAddress','shipment.deliveryAddress');
        $this->orderProduct = $orderProduct;
        $firstShipment = $this->orderProduct->shipment->first();
        $firstShipment->loadMissing('shippingProvider');
        $this->shippingProviderModel = $firstShipment->shippingProvider;
        $this->shippingProvider = $this->shippingService->provider($this->shippingProviderModel->code)->getProvider();
        // $this->shippingProvider = $this->shippingService->provider('shiprocket')->getProvider();
    }

    public function return():bool
    {
        foreach ($this->orderProduct->shipment as $orderShipment)
        {
            if ($this->orderProduct->product->is_returnable)
            {
                $returnInfo = $this->shippingProvider->return()->create($this->orderProduct,$orderShipment);


                $orderShipment->fill([
                    'status'            => OrderShipment::RETURNING,
                    'return_order_id'   => $returnInfo['order_id'],
                    'return_shipment_id'   => $returnInfo['shipment_id'],
                    'details' => array_merge($orderShipment->details,[
                        'return_details' => $returnInfo
                    ])
                ])->save();
            }else{
                $this->error = 'Product sku: '.$this->orderProduct->product->sku.' is non returnable';
            }

        }
        return is_null($this->error);
    }


    public function getError(): ?string
    {
        return $this->error;
    }

}
