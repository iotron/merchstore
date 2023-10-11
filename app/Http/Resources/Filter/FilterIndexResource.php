<?php

namespace App\Http\Resources\Filter;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilterIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return ['code' => $this->code,
        'display_name' => $this->display_name,
       // 'options' => $this->options,
        'options' => FilterOptionIndexResource::collection($this->options),
    ];
    }
}
