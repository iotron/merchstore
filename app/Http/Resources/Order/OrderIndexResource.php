<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use App\Models\Order\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'amount' => $this->amount->formatted(),
            'subtotal' => $this->subtotal->formatted(),
            'total' => $this->total->formatted(),
            'voucher' => $this->voucher,
            'quantity' => $this->quantity,
            'tracking_id' => $this->tracking_id,
            'status' => Order::StatusOptions[$this->status],


        ];
    }
}
