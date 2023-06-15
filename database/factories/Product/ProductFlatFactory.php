<?php

namespace Database\Factories\Product;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product\ProductFlat>
 */
class ProductFlatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => fake()->paragraphs(3),
            'short_description' => fake()->text(180),
            'height' => 23,
            'width' => 324,
            'length' => 233,
            'weight' => 232,
        ];
    }
}
