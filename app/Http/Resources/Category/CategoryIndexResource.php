<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryIndexResource extends JsonResource
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
            'status' => $this->status,
            'is_visible_on_front' => $this->is_visible_on_front,
            'view_count' => $this->view_count,
            'order' => $this->order,
            'children' => CategoryIndexResource::collection($this->whenLoaded('children')),
        ];
    }
}
