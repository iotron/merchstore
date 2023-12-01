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
        $demoCustomer->addresses()->create($this->getAddressArray());
        $demoCustomer->addresses()->create($this->getAddressArray());
        $demoCustomer->addresses()->create($this->getAddressArray());



        Customer::factory(20)
            ->create([

                // Add Customer to Customer Group
                'customer_group_id' => $customerGroups->random()->id
            ])
            ->each(function (Customer $customer){

                // Create Customer Address
                $customer->addresses()->create($this->getAddressArray());
            });
    }


    public function getAddressArray():array
    {
        return [
            'name' => fake()->word.' Address',
            'contact' => fake()->numerify('##########'),
            'alternate_contact' => '',
            'type' => 'Home',
            'address_1' => fake()->address,
            'address_2' => 'Line Two',
            'landmark' => '',
            'city' => fake()->randomElement(['Delhi','Kolkata','Mumbai']),
            'postal_code' => 700055,
            'state' => fake()->randomElement(['del','wb','mum']),
            'default' => 1,
            'priority' => 1,
            'country_code' => 'IN',
        ];
    }

}
