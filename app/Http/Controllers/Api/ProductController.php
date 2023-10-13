<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductIndexResource;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Category\ThemeResource;
use App\Http\Resources\Filter\FilterIndexResource;
use App\Models\Category\Category;
use App\Models\Category\Theme;
use App\Models\Filter\FilterGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{

    /**
     * Display a listing of the products with filters.
     */
    public function index(Request $request)
    {
        // base query
        $query = Product::where('status', Product::PUBLISHED);
        // finding all the filters before pagination
        $filterGroupIds = $query->pluck('filter_group_id')->unique()->toArray();

        $filterGroups = FilterGroup::whereIn('id', $filterGroupIds)->with('filters.options')->get();
        // Remove duplicates from the filters relationship and flattens structure
        $filters = $filterGroups->flatMap(function ($filterGroup) {
            return $filterGroup->filters;
        })->unique('id');

        // get product themes
        $themes = $query
            ->with('themes')
            ->get()
            ->pluck('themes')
            ->flatten();

        // additional information
        $query->with('media');
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


        $products = $query->paginate(12);
        // dd($products);
        return ProductIndexResource::collection($products)
            ->additional([
                'filters' => FilterIndexResource::collection($filters),
                'themes' => $themes
            ]);
    }



    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('flat', 'media', 'filterOptions', 'filterOptions.filter', 'themes', 'feedbacks');
        return ProductResource::make($product);
    }

    public function showProductsByCategory(Category $category)
    {

        $category->load('children');
        // base query
        $query = Product::where('status', Product::PUBLISHED)->whereHas('categories', function ($query) use ($category) {
            $query->where('categories.id', $category->id);
        });
        // finding all the filters before pagination
        $filterGroupIds = $query->pluck('filter_group_id')->unique()->toArray();
        $filterGroups = FilterGroup::whereIn('id', $filterGroupIds)->with('filters.options')->get();
        // Remove duplicates from the filters relationship and flattens structure
        $filters = $filterGroups->flatMap(function ($filterGroup) {
            return $filterGroup->filters;
        })->unique('id');


        // get product themes
        $themes = $query
            ->with('themes')
            ->get()
            ->pluck('themes')
            ->flatten();

        // additional information
        $query->with('media');
        $products = $query->paginate();
        return ProductIndexResource::collection($products)
            ->additional([
                'categories' => CategoryResource::collection($category->children),
                'filters' => FilterIndexResource::collection($filters),
                'themes' => ThemeResource::collection($themes)
            ]);
    }

    public function showProductsByTheme(Theme $theme)
    {

        $theme->load('children');
        $query = Product::where('status', Product::PUBLISHED)
            ->whereHas('themes', function ($query) use ($theme) {
                $query->where('themes.id', $theme->id);
            });
        // finding all the filters before pagination
        $filterGroupIds = $query->pluck('filter_group_id')->unique()->toArray();
        $filterGroups = FilterGroup::whereIn('id', $filterGroupIds)->with('filters.options')->get();
        // Remove duplicates from the filters relationship and flattens structure
        $filters = $filterGroups->flatMap(function ($filterGroup) {
            return $filterGroup->filters;
        })->unique('id');


        // get product categories
        $categories = $query
            ->with('categories')
            ->get()
            ->pluck('categories')
            ->flatten();

        // additional information
        $query->with('media');
        $products = $query->paginate();

        return ProductIndexResource::collection($products)->additional([
            'categories' => CategoryResource::collection($categories),
            'filters' => FilterIndexResource::collection($filters),
            'themes' => ThemeResource::collection($theme->children)
        ]);
        ;
    }

}
