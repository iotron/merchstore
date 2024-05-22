<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Product\ProductIndexResource;
use App\Services\Iotron\MoneyService\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product' => ProductIndexResource::make($this->whenLoaded('product')),
            'quantity' => $this->quantity,
            'amount' => ($this->amount instanceof Money) ? $this->amount->formatted() : $this->amount,
            'discount' => ($this->discount instanceof Money) ? $this->discount->formatted() : $this->discount,
            'tax' => ($this->tax instanceof Money) ? $this->tax->formatted() : $this->tax,
            'total' => ($this->total instanceof Money) ? $this->total->formatted() : $this->total,
            'has_tax' => $this->has_tax,
        ];
    }
}
