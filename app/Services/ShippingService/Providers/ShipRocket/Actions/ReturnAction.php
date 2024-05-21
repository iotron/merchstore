<?php

namespace App\Services\ShippingService\Providers\ShipRocket\Actions;

use App\Models\Order\OrderProduct;
use App\Models\Order\OrderShipment;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderReturnContract;
use App\Services\ShippingService\Providers\ShipRocket\ShipRocketApi;

class ReturnAction implements ShippingProviderReturnContract
{
    protected ShipRocketApi $api;

    public function __construct(ShipRocketApi $api)
    {
        $this->api = $api;
    }

    public function create(OrderProduct $orderProduct, OrderShipment $orderShipment): mixed
    {
        dd($orderShipment, $this->format($orderProduct, $orderShipment));
    }

    protected function format(OrderProduct $orderProduct, OrderShipment $orderShipment)
    {
        return [
            'name' => $orderProduct->product->name,
            'sku' => $orderProduct->product->sku,
            'units' => $orderProduct->quantity,
            'selling_price' => $orderProduct->product->price->getAmount(),
            'payment_method' => '', // Prepaid/COD
            'sub_total' => '',
            'length' => '',
            'breadth' => '',
            'height' => '',
            'weight' => '',

            'order_id' => $orderShipment->provider_order_id, // provider order
            'order_date' => null,
            'channel_id' => $orderShipment->provider_channel_id,
            'pickup_customer_name' => $orderProduct->order->customer->name,
            'pickup_address' => $orderShipment->deliveryAddress->type,
            'pickup_address_2' => $orderShipment->deliveryAddress->address_1,
            'pickup_city' => $orderShipment->deliveryAddress->city,
            'pickup_state' => $orderShipment->deliveryAddress->state,
            'pickup_country' => $orderShipment->deliveryAddress->country_code,
            'pickup_pincode' => $orderShipment->deliveryAddress->postal_code,
            'pickup_email' => $orderProduct->order->customer->email,
            'pickup_phone' => $orderProduct->order->customer->contact,

            'shipping_customer_name' => '',
            'shipping_address' => $orderShipment->pickupAddress->type,
            'shipping_address_2' => $orderShipment->pickupAddress->address_1,
            'shipping_city' => $orderShipment->pickupAddress->city,
            'shipping_state' => $orderShipment->pickupAddress->state,
            'shipping_country' => $orderShipment->pickupAddress->country_code,
            'shipping_pincode' => $orderShipment->pickupAddress->postal_code,
            'shipping_email' => '',
            'shipping_phone' => '',
            'order_items' => [
                [
                    'sku' => $orderProduct->product->sku,
                    'name' => $orderProduct->product->name,
                    'units' => $orderShipment->total_quantity,
                    'selling_price' => $orderProduct->product->price->getAmount(),
                    //                    "discount" => 0,
                    //                    "qc_enable" => true,
                    //                    "hsn" => "123",
                    //                    "brand" => "",
                    //                    "qc_size" => "43"
                ],
            ],

        ];
    }
}
