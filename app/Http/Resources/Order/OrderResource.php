<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\Location\AddressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends OrderIndexResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       // dd($this);

        return array_merge(parent::toArray($request),[

            'subtotal' => $this->subtotal->formatted(),
            'total' => $this->total->formatted(),
            'payment_success' => $this->payment_success,
            'customer_gstin' => $this->customer_gstin,
            'shipping_is_billing' => $this->shipping_is_billing,
            'order_products' => OrderProductResource::collection($this->whenLoaded('orderProducts')),

            'invoice' => OrderInvoiceResource::collection($this->whenLoaded('invoices')),
            'shipping_address' => AddressResource::make($this->whenLoaded('billingAddress'))
        ]);
    }
}
