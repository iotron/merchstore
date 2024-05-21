<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryIndexResource;
use App\Http\Resources\Category\ThemeResource;
use App\Http\Resources\Product\ProductIndexResource;
use App\Models\Category\Category;
use App\Models\Category\Theme;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', true)->with('children', 'children.children', 'media')->parents()->get();

        return CategoryIndexResource::collection($categories);
    }

    public function show(Category $category)
    {
        $category->load('children', 'children.children');
        $productsPaginated = $category->products()->with('flat')->orderByDesc('view_count')->paginate(12);

        return ProductIndexResource::collection($productsPaginated)->additional(['category' => new CategoryIndexResource($category)]);
    }

    public function AllThemes()
    {
        $themes = Theme::with('children', 'children.children')->parents()->get();

        return ThemeResource::collection($themes);
    }
}
