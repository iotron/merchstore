<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

       return [
        'name' => $this->name,
        'url' => $this->url,
        'sku' => $this->sku,
        'quantity' => ($this->quantity != 0) ? $this->quantity : $this->availableStocks->sum('in_stock_quantity'),
        'popularity' => $this->popularity,
        'view_count' => $this->view_count,
        'price' => $this->price->formatted(),
        'type' => $this->type,
        'returnable' => $this->is_returnable,

        'productDisplay' => [
            'src' => $this->getFirstMediaUrl('productDisplay'),
            'srcset' => $this->getFirstMedia('productDisplay') ? $this->getFirstMedia('productDisplay')->getSrcset('optimized') : null,
        ],

    ];
    }
}
