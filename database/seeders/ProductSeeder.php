<?php

namespace Database\Seeders;

use App\Models\Category\Category;
use App\Models\Category\Theme;
use App\Models\Filter\Filter;
use App\Models\Filter\FilterGroup;
use App\Models\Localization\Address;
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
        $parentThemes = Theme::Parents()->with('children')->get();

        // Create Simple
        $this->simpleProductSeed($parentCategories,$parentThemes);

        // Create Configurable
       // $this->configurableProductSeeding($parentCategories,$parentThemes);



    }


    public function simpleProductSeed($parentCategories,$parentThemes)
    {
        // Create Product + Product Flat
        $parentCategories->each(function(Category $category) use($parentThemes) {
            Product::factory()->count(10)
                ->hasFlat(1)
                ->create()
                ->each(function (Product $product) use($category, $parentThemes){
                    // Add Category (Parent)
                    $product->categories()->attach($category->id,['base_category' => true]);
                    // Add Category (Children)
                    if ($category->children->count())
                    {
                        $product->categories()->attach($category->children->random(2));
                    }



                    // adding parent theme
                    $selectedParentTheme = $parentThemes->random();
                    $product->themes()->attach($selectedParentTheme);
                    // adding children themes
                    $themes = $selectedParentTheme->children->random(2);
                    $product->themes()->attach($themes);


                    // Add Stock
                    $stock1 = $product->stocks()->create([
//                        'init_quantity' => fake()->numberBetween(200, 600),
                        'init_quantity' => fake()->randomElement([200,300]),
                        'sold_quantity' => 0,
                    ]);
                    $stock1Address = $stock1->addresses()->create(Address::factory()->raw());
                    $stock1->update(['address_id' => $stock1Address->id]);


                    $stock2 = $product->stocks()->create([
                        //'init_quantity' => fake()->numberBetween(200, 600),
                        'init_quantity' => fake()->randomElement([50,150]),
                        'sold_quantity' => 0,
                    ]);

                    $stock2Address = $stock2->addresses()->create(Address::factory()->raw());
                    $stock2->update(['address_id' => $stock2Address->id]);

                    $stock3 = $product->stocks()->create([
                        //'init_quantity' => fake()->numberBetween(200, 600),
                        'init_quantity' => fake()->randomElement([100,200]),
                        'sold_quantity' => 0,
                    ]);
                    $stock3Address = $stock3->addresses()->create(Address::factory()->raw());
                    $stock3->update(['address_id' => $stock3Address->id]);


                    // Attach Filter Options
                    $filterGroup = FilterGroup::with('filters','filters.options')->firstWhere('id',$product->filter_group_id);
                    $filterGroup->filters->map(function ($filter) use($product) {
                        $product->filterOptions()->attach($filter->options->first->id);
                    });


                });


        });
    }



    public function configurableProductSeeding($parentCategories,$parentThemes)
    {

        $parentCategories->each(function(Category $category) use($parentThemes) {
            $rawProductData = Product::factory()->raw(['type' => Product::CONFIGURABLE]);
            $typeInstance = app(config('project.product_types.'.$rawProductData['type'].'.class'));
            $rawProductData['filter_attributes'] = $this->getFilterDetails($rawProductData['filter_group_id']);
            // Create Configurable Product
            $product = $typeInstance->create($rawProductData);
        });

    }




    private function getFilterDetails(int $id): array
    {
        $group = FilterGroup::where('id', $id)->with('filters.options')->first();
        $bag=[];
        foreach ($group->filters as $filter)
        {
            $options = $filter->options->random(random_int(1,3))->pluck('display_name','id')->toArray();
            $bag [$filter->display_name] = $options;
        }
        return $bag;

    }


}
