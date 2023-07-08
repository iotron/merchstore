<?php

namespace Database\Seeders;

use App\Models\Customer\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::factory(20)
            ->create()
            ->each(function (Customer $customer){

                // Create Customer Address
                $customer->addresses()->create([
                    'name' => fake()->word.' Address',
                    'contact' => fake()->numerify('##########'),
                    'alternate_contact' => '',
                    'type' => 'Home',
                    'address_1' => fake()->address,
                    'address_2' => 'Line Two',
                    'landmark' => '',
                    'city' => 'Kolkata',
                    'postal_code' => 700001,
                    'state' => 'wb',
                    'default' => 1,
                    'priority' => 1,
                    'country_code' => 'IN',
                ]);
                $customer->save();
            });
    }
}
