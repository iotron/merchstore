<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'src' => $this->resource->fallbackUrls,  // need to update later (just for test)
            //   'src' => $this->getUrl(),
            //  'srcset' => $this->getSrcset('optimized'),
            //   'srcset' => $this->whenNotNull($this->responsive_images),
        ];
    }
}
