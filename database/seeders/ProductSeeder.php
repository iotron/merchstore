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

        $filterGroup = FilterGroup::with('filters','filters.options')->whereIn('id',[5,6])->get();

        $parentCategories->each(function(Category $category) use($filterGroup){

            $filterGroupId = $filterGroup->shuffle()->all()[0]->id;
            $selectedFilterGroup = $filterGroup->firstWhere('id',$filterGroupId);
            $filterOptions = $selectedFilterGroup->filters->map(function (Filter $filter){

                $optionBag[$filter['code']] = $filter->options->take(3)->mapWithKeys(function ($item, $key) use($filter){
                    return [$item['admin_name'] => $item['admin_name']];

                })->toArray();
                return $optionBag;

            })->toArray();


            $filter_attributes = [];

            foreach ($filterOptions as $option)
            {
                foreach ($option as $key => $value)
                {
                    $filter_attributes[$key] = array_values($value);
                }
            }


            Product::factory()->count(10)
                ->hasFlat(1,[
                    'filter_attributes' => $filter_attributes
                ])
                ->create(['filter_group_id' => $filterGroupId])
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



                });


        });


    }
}
