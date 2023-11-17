<?php

namespace Database\Seeders;

use App\Models\Customer\Customer;
use App\Models\Customer\CustomerGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $customerGroups = CustomerGroup::where('status',true)->get();

        $demoCustomer = Customer::create([
            'name' => 'Demo Customer',
            'email' => 'customer@example.com',
            'contact' => '1234567890',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'contact_verified_at' => now(),
            'customer_group_id' => $customerGroups->random()->id
        ]);

        // Create Customer Address
        $demoCustomer->addresses()->create([
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
        $demoCustomer->save();



        Customer::factory(20)
            ->create([

                // Add Customer to Customer Group
                'customer_group_id' => $customerGroups->random()->id
            ])
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
