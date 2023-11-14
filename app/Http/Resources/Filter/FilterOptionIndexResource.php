<?php

namespace App\Http\Resources\Filter;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilterOptionIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            //'display_name ' => $this->filter->display_name,
            'value' => $this->display_name,
        ];
    }
}
