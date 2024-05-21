<?php

namespace Database\Seeders;

use App\Models\Product\ProductFeedback;
use Illuminate\Database\Seeder;

class ProductFeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductFeedback::factory(60)->create();
    }
}
