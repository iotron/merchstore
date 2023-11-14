<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Category\ThemeResource;
use App\Http\Resources\Filter\FilterOptionIndexResource;
use App\Http\Resources\Filter\FilterOptionResource;
use App\Http\Resources\MediaResource;
use App\Models\Filter\FilterOption;
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
            'in_stock' => (bool) $this->availableStocks()->count(),
            'flat' => new ProductFlatResource($this->whenLoaded('flat')),
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d-m-Y'),
            //'filter_options' => FilterOptionIndexResource::collection($this->whenLoaded('filterOptions')),
            'filter_options' => $this->getFilterOptions(),
            'themes' => ThemeResource::collection($this->whenLoaded('themes')),
            'feedbacks' => ProductFeedbackResource::collection($this->whenLoaded('feedbacks'))
        ]);
    }

    public function getFilterOptions(): array
    {
        $bag = [];
        foreach ($this->filterOptions as $option)
        {
            $bag[$option->filter->display_name] []= $option->admin_name;
        }
        return $bag;
    }


}
