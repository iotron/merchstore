<?php

namespace Database\Factories\Product;

use App\Models\Customer\Customer;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product\ProductFeedback>
 */
class ProductFeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::where('status', Product::PUBLISHED)->get()->random()->id,
            'customer_id' => Customer::all()->random()->id,
            'rating' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'comment' => $this->faker->sentence(10)
        ];
    }
}
