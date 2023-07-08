<?php

namespace Database\Seeders;

use App\Models\Customer\CustomerGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $regularCustomerGroup = CustomerGroup::create([
            'name' => 'Regular',
            'status' => true,
        ]);

        $exclusiveCustomerGroup = CustomerGroup::create([
            'name' => 'Exclusive',
            'status' => true,
        ]);


    }
}
