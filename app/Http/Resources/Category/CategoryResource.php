<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;

class CategoryResource extends CategoryIndexResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [

        ]);
    }
}
