<?php

namespace Database\Seeders;

use App\Models\Category\Category;
use App\Models\Product\Product;
use App\Models\Product\ProductFlat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parentCategories = Category::Parents()->orderBy('name')->get();
        // Create Product + Product Flat
        Product::factory()->count(50)
            ->hasFlat(1)
            ->create()
            ->each(function (Product $product) use($parentCategories){
                $product->categories()->attach($parentCategories->random()->id,['base_category' => true]);
            })
        ;
    }
}
