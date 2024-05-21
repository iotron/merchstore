<?php

namespace App\Services\ShippingService\Providers\ShipRocket\Support;

use App\Models\Order\Order;
use App\Models\Order\OrderShipment;
use Illuminate\Support\Facades\Validator;

trait hasShippableOrders
{
    public function format(OrderShipment $orderShipment): array
    {
        return $this->getOrderableArrayFromOrderModel($orderShipment);
    }

    protected function getOrderableArrayFromOrderModel(OrderShipment $orderShipment): array
    {

        $orderShipment->loadMissing('order', 'orderProducts', 'orderProducts.product', 'deliveryAddress', 'deliveryAddress.country');

        $order = $orderShipment->order;
        $order->loadMissing('customer', 'orderProducts', 'billingAddress', 'billingAddress.country');

        $orderItems = $orderShipment->orderProducts->map(function ($item) {
            return [
                'name' => $item->product->name,
                'sku' => $item->product->sku,
                'units' => $item->quantity,
                'selling_price' => $item->total->getAmount(),
                'discount' => $item->discount->getAmount(),
                'tax' => $item->tax->getAmount(),
            ];
        })->toArray();

        return [
            'order_id' => $order->id, // need to check
            'order_date' => $order->created_at->format('Y-m-d H:i'),
            'order_items' => $orderItems,
            'payment_method' => ($orderShipment->cod) ? 'COD' : 'Prepaid',
            'shipping_charges' => $orderShipment->charge->getAmount(),
            'giftwrap_charges' => 0.00,
            'transaction_charges' => 0.00,
            'total_discount' => $order->discount->getAmount(),
            'sub_total' => $order->subtotal->getAmount(),

            // From Shipment
            'pickup_location' => $orderShipment->pickupAddress->address_1,
            //'channel_id'                => $order->channel_id,
            'comment' => '',
            'length' => $orderShipment->length,
            'breadth' => $orderShipment->breadth,
            'height' => $orderShipment->height,
            'weight' => $orderShipment->weight,

            // Billing
            'billing_customer_name' => $order->billingAddress->name,
            'billing_last_name' => '',
            'billing_email' => $order->customer->email,
            'billing_phone' => $order->billingAddress->contact,
            'billing_address' => $order->billingAddress->address_1,
            'billing_address_2' => $order->billingAddress->address_2,
            'billing_city' => $order->billingAddress->city,
            'billing_pincode' => $order->billingAddress->postal_code,
            'billing_state' => $order->billingAddress->state,
            'billing_country' => $order->billingAddress->country->name,
            // Shipping Is Billing
            'shipping_is_billing' => $order->shipping_is_billing,
            // Shipping
            'shipping_customer_name' => $orderShipment->deliveryAddress->name,
            'shipping_last_name' => '',
            'shipping_email' => $order->customer->email,
            'shipping_phone' => $orderShipment->deliveryAddress->contact,
            'shipping_address' => $orderShipment->deliveryAddress->address_1,
            'shipping_address_2' => $orderShipment->deliveryAddress->address_2,
            'shipping_city' => $orderShipment->deliveryAddress->city,
            'shipping_pincode' => $orderShipment->deliveryAddress->postal_code,
            'shipping_state' => $orderShipment->deliveryAddress->state,
            'shipping_country' => $orderShipment->deliveryAddress->country->name,

        ];
    }

    //
    //
    //
    //    public function getDefaultOrderBody(array|object $order = []):string
    //    {
    //        if ($this->orderFormatValidator($order)) {
    //            return json_encode([
    //                'order_id' => $order['order_id'] ,
    //                'order_date' => $order['order_date'],
    //                'pickup_location' => $order['pickup_location'],
    //                'channel_id' => $order['channel_id'],
    //                'comment' => $order['comment'],
    //                'billing_customer_name' => $order['billing_customer_name'],
    //                'billing_last_name' => $order['billing_last_name'],
    //                'billing_address' => $order['billing_address'],
    //                'billing_address_2' => $order['billing_address_2'],
    //                'billing_city' => $order['billing_city'] ,
    //                'billing_pincode' => $order['billing_pincode'],
    //                'billing_state' => $order['billing_state'],
    //                'billing_country' => $order['billing_country'],
    //                'billing_email' => $order['billing_email'],
    //                'billing_phone' => $order['billing_phone'],
    //                'shipping_is_billing' => $order['shipping_is_billing'],
    //                'shipping_customer_name' => $order['shipping_customer_name'],
    //                'shipping_last_name' => $order['shipping_last_name'],
    //                'shipping_address' => $order['shipping_address'],
    //                'shipping_address_2' => $order['shipping_address_2'] ,
    //                'shipping_city' => $order['shipping_city'],
    //                'shipping_pincode' => $order['shipping_pincode'],
    //                'shipping_country' => $order['shipping_country'],
    //                'shipping_state' => $order['shipping_state'],
    //                'shipping_email' => $order['shipping_email'],
    //                'shipping_phone' => $order['shipping_phone'],
    //                'order_items' => $order['order_items'],
    //                'payment_method' => $order['payment_method'] ,
    //                'shipping_charges' => $order['shipping_charges'],
    //                'giftwrap_charges' => $order['giftwrap_charges'],
    //                'transaction_charges' => $order['transaction_charges'],
    //                'total_discount' => $order['total_discount'],
    //                'sub_total' => $order['sub_total'] ,
    //                'length' => $order['length'],
    //                'breadth' => $order['breadth'],
    //                'height' => $order['height'],
    //                'weight' => $order['weight'] ,
    //            ]);
    //        }
    //    }
    //
    //
    //
    //
    //
    //    public function orderFormatValidator(array $order): bool
    //    {
    //        $validator = Validator::make($order, [
    //            'order_id' => 'required|string',
    //            'order_date' => 'required|string',
    //            'pickup_location' => 'required|string',
    //            'billing_customer_name' => 'required|string',
    //            'billing_city' => 'required|string',
    //            'billing_pincode' => 'required|int',
    //            'billing_state' => 'required|string',
    //            'billing_country' => 'required|string',
    //            'billing_email' => 'required|string',
    //            'billing_phone' => 'required|string',
    //            'shipping_is_billing' => 'required|string',
    //            'order_items' => 'required|array',
    //            'name' => 'required|string',
    //            'sku' => 'required|string',
    //            'units' => 'required|string',
    //            'selling_price' => 'required|string',
    //            'payment_method' => 'required|string',
    //            'sub_total' => 'required|string',
    //            'length' => 'required|float',
    //            'breadth' => 'required|float',
    //            'weight' => 'required|float',
    //        ]);
    //
    //        return ! $validator->fails();
    //    }

}
