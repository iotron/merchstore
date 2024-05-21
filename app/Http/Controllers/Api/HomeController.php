<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductIndexResource;
use App\Models\Product\Product;

class HomeController extends Controller
{
    public function getPopularProducts()
    {
        $products = Product::where('status', Product::PUBLISHED)->orderBy('popularity')->take(20)->get();

        return ProductIndexResource::collection($products);
    }
}
