<?php

namespace Database\Seeders;

use App\Models\Customer\CustomerGroup;
use App\Models\Promotion\Sale;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allCustomerGroups = CustomerGroup::all();

        $sales = Sale::factory()
            ->count(10)
            ->create()
            ->each(function (Sale $sale) use ($allCustomerGroups) {
                $sale->customer_groups()->attach($allCustomerGroups->random());
            });
    }
}
