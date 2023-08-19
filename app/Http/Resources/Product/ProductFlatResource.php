<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductFlatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'short_description' => $this->short_description,
            'description' => $this->description,
            'width' => $this->width,
            'height' => $this->height,
            'length' => $this->length,
            'weight' => $this->weight,
            'filter_attributes' => $this->filter_attributes,
            'return_window' => $this->return_window,
            'production_time' => $this->production_time,
            'meta_data' => $this->meta_data,

        ];
    }
}
