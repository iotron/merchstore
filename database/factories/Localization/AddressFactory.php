<?php

namespace Database\Factories\Localization;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Localization\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'contact' => fake()->numerify('##########'),
            'alternate_contact' => fake()->optional()->phoneNumber,
            'type' => fake()->randomElement(['Home', 'Work', 'Other']),
            'address_1' => fake()->streetAddress,
            'address_2' => fake()->optional()->secondaryAddress,
            'landmark' => fake()->optional()->streetName,
            'city' => fake()->city,
            'postal_code' => fake()->postcode,
            'state' => fake()->state,
            'default' => fake()->boolean,
            'priority' => fake()->numberBetween(1, 10),
            'country_code' => 'IN',
        ];
    }
}
