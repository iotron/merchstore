<?php

namespace Database\Factories\Promotion;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion\VoucherCode>
 */
class VoucherCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->word,
            'coupon_usage_limit' => fake()->numberBetween(0, 100),
            'usage_per_customer' => fake()->numberBetween(0, 5),
            'times_used' => 0,
            'type' => fake()->numberBetween(0, 2),
            'starts_from' => now(),
            'ends_till' => now()->addDays(21),
        ];
    }
}
