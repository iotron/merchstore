<?php

namespace Database\Seeders;

use App\Models\Category\Category;
use App\Models\Filter\Filter;
use App\Models\Filter\FilterGroup;
use App\Models\Product\Product;
use App\Models\Product\ProductFlat;
use App\Models\Product\ProductStock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parentCategories = Category::with('children')->whereHas('children')->parents()->where('status', true)->get();
        // Create Product + Product Flat

        $parentCategories->each(function(Category $category) {
            Product::factory()->count(10)
                ->hasFlat(1)
                ->create()
                ->each(function (Product $product) use($category){
                    // Add Category (Parent)
                    $product->categories()->attach($category->id,['base_category' => true]);
                    // Add Category (Children)
                    if ($category->children->count())
                    {
                        $product->categories()->attach($category->children->random(2));
                    }

                    // Add Stock
                    $stock = $product->stocks()->create([
                        'init_quantity' => fake()->numberBetween(200, 600),
                        'sold_quantity' => 0,
                    ]);

                    $stock2 = $product->stocks()->create([
                        'init_quantity' => fake()->numberBetween(200, 600),
                        'sold_quantity' => 0,
                    ]);

                    $stock3 = $product->stocks()->create([
                        'init_quantity' => fake()->numberBetween(200, 600),
                        'sold_quantity' => 0,
                    ]);


                    // Attach Filter Options
                    $filterGroup = FilterGroup::with('filters','filters.options')->firstWhere('id',$product->filter_group_id);

                    $filterGroup->filters->map(function ($filter) use($product) {
                        $product->filterOptions()->attach($filter->options->first->id);
                    });

                    
                });


        });


    }
}
