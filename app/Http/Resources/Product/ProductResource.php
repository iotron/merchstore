<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\MediaResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends ProductIndexResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return array_merge(parent::toArray($request),[
//
//            'productGallerys' => [
//                'src' => $this->getFirstMediaUrl('productGallery'),
//                'srcset' => $this->getFirstMedia('productGallery') ? $this->getFirstMedia('productGallery')->getSrcset('optimized') : null,
//            ],
            'productGallery' => new MediaResource($this->getMediaCollection('productGallery')) , // temp

            'featured' => $this->featured,
            'status' => $this->status,
            'visible_individually' => $this->visible_individually,
            'base_price' => $this->base_price->formatted(),
            'hsn_code' => $this->hsn_code,
            'tax_percent' => $this->tax_percent,
            'tax_amount' => $this->tax_amount,
            'min_range' => $this->min_range,
            'max_range' => $this->max_range,
            'flat' => new ProductFlatResource($this->whenLoaded('flat')),
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d-m-Y'),
        ]);
    }
}
