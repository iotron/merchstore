<?php

namespace App\Http\Resources\Filter;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilterOptionResource extends FilterOptionIndexResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request),[
            'swatch_value' => $this->swatch_value,
            'position' => $this->position
        ]);
    }
}
