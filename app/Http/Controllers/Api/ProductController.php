<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductIndexResource;
use App\Models\Product\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{


    /**
     * route use 'api/v1/events/filters/all'
     * @return JsonResponse|AnonymousResourceCollection
     * Filter List
     */
    public function getFilterOptions(): JsonResponse|AnonymousResourceCollection
    {

        $data = [
            [
                'name' => 'city',
                'multiselect' => true,
                'options' => City::where('status', true)->get(['name','cities.url as value']),
            ],
            [
                'name' => 'time',
                'multiselect' => false,
                'options' => [
                    ['name' => 'today','value' => 'today'],
                    ['name' => 'tomorrow','value' => 'tomorrow'],
                    ['name' => 'weekend','value' => 'weekend']
                ],
            ],
            [
                'name' => 'genre',
                'multiselect' => true,
                'options' => Genre::where('parent_id', null)->where('status', true)->get(['name','genres.url as value']),
            ],
            [
                'name' => 'language',
                'multiselect' => true,
                'options' => collect(FilterOption::where('filter_type', 'Languages')->get('name','filter_options.name as value'))->map(function ($query){
                    return [
                        'name' => $query->name,
                        'value' => $query->name
                    ];
                }),
            ],
            [
                'name' => 'artist',
                'multiselect' => true,
                'options' => Artist::where('status', true)->get(['name', 'artists.url as value']),
            ],
            [
                'name' => 'view',
                'multiselect' => false,
                'options' => [
                    'name' => 'value',
                    'value' => 'value',
                ],
            ],
            [
                'name' => 'type',
                'multiselect' => false,
                'options' => [
                    ['name' => Events::ONLINE_PREMIER , 'value' => Events::ONLINE_PREMIER],
                    ['name' => Events::OUTDOOR, 'value' => Events::OUTDOOR]
                ],
            ]
        ];


        return response()->json([
           // 'data' => Product::availableScopesWithOption(['price','view']),
            'data' => $data,
        ], 200);

    }



    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

//        $validator = $request->validate($request->filter,[
//
//        ]);


      //  $filters = $request->input('filter', []);

// Non-static usage
//        $products = Product::where('status', Product::PUBLISHED)
//            ->filter($request,['price','view'])
//            ->latest()
//            ->paginate(12);

// Static usage
        $query = Product::where('status', Product::PUBLISHED);

        if ($request->query('filter'))
        {
            $query->filter($request->query('filter'),['price','view']);
        }
//
//        if ($request->query('sort'))
//        {
//            $query->sort($request->query('sort'));
//        }else{
//            $query->latest();
//        }


        $products= $query->paginate(12);


        return $products;


        return ProductIndexResource::collection($products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
