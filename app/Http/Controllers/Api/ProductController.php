<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductIndexResource;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{




    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Product::with('flat','media')->where('status', Product::PUBLISHED);

    //    if ($request->query('filter'))
    //    {
    //        $query->filter($request->query('filter'),['price','view']);
    //    }

    //    if ($request->query('sort'))
    //    {
    //        $query->sort($request->query('sort'));
    //    }else{
    //        $query->latest();
    //    }


        $products= $query->simplePaginate(12);
        return ProductIndexResource::collection($products);
    }



    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('flat','media','filterOptions','filterOptions.filter', 'themes', 'feedbacks');
        return ProductResource::make($product);
    }


}
