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
            'starts_from' => now(),
            'ends_till' =>  now()->addDays(20),
            'status' => fake()->boolean,
            'condition_type' => 1,
            'end_other_rules' => fake()->boolean,
            'action_type' => fake()->randomElement( ['by_percent','by_fixed']),
            'discount_amount' => fake()->numberBetween(2000,6000),
            'sort_order' => 1,
            'conditions' => $this->preSetConditions()
        ];
    }


    private function preSetConditions(): array
    {
        return [
            [
                "attribute" =>  "attribute|price",
                "value"     =>  fake()->numberBetween(100,300),
                "operator"  =>  ">"
            ],
            [
                "attribute" =>"attribute|color",
                "value" => fake()->numberBetween(1,8),
                "operator" => ">"
            ]
        ];
    }


}
