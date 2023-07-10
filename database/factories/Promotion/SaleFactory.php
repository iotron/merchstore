<?php

namespace Database\Factories\Promotion;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word,
            'description' => fake()->paragraph,
            'starts_from' => fake()->date(),
            'ends_till' => now()->addDays(20),
            'status' => fake()->boolean,
            'condition_type' => fake()->boolean(10), // 80% chance of true
            'conditions' => [],
            'end_other_rules' => fake()->boolean,
            'action_type' => fake()->randomElement(['discount', 'special_offer']),
            'discount_amount' => fake()->randomNumber(3),
            'sort_order' => fake()->randomNumber(2),
        ];
    }
}
