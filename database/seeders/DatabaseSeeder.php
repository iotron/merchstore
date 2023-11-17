<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(array_merge($this->getProductionSeeders(),$this->getLocalSeeders()));
    }




    protected function getProductionSeeders(): array
    {
        return [
            UserSeeder::class,
            CountrySeeder::class,
            CustomerGroupSeeder::class,
            FilterSeeder::class,
            CategorySeeder::class,
            PaymentProviderSeeder::class,
            ShippingProviderSeeder::class,
        ];
    }


    protected function getLocalSeeders(): array
    {
        return [
            CustomerSeeder::class,
            ThemeSeeder::class,
            ProductSeeder::class,
            SaleSeeder::class,
            VoucherSeeder::class,
            ProductFeedbackSeeder::class
        ];
    }


}
