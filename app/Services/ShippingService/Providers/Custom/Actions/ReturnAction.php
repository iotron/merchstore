<?php

namespace App\Services\ShippingService\Providers\Custom\Actions;

use App\Models\Order\OrderProduct;
use App\Models\Order\OrderShipment;
use App\Services\ShippingService\Contracts\Provider\ShippingProviderReturnContract;

class ReturnAction implements ShippingProviderReturnContract
{
    public function create(OrderProduct $orderProduct, OrderShipment $orderShipment): mixed
    {
        $default = [
            'order_id' => $orderShipment->provider_order_id,
            'shipment_id' => $orderShipment->shipment_id,
            'status' => 'RETURN PENDING',
            'status_code' => 21,
            'company_name' => 'custom',
        ];

        return $default;
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

            'order_id' => null, // provider order
            'order_date' => null,
            'channel_id' => null,
            'pickup_customer_name' => null,
            'pickup_address' => null, // Home
            'pickup_city' => null,
            'pickup_state' => null,
            'pickup_country' => null,
            'pickup_pincode' => null,
            'pickup_email' => null,
            'pickup_phone' => null,
            'shipping_customer_name' => null,
            'shipping_address' => null, // Home
            'shipping_city' => null,
            'shipping_state' => null,
            'shipping_country' => null,
            'shipping_pincode' => null,
            'shipping_email' => null,
            'shipping_phone' => null,
            'order_items' => [],

        ];
    }
}
