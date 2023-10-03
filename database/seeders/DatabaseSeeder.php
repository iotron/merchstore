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
        $this->call([
            UserSeeder::class,
            CountrySeeder::class,
            CustomerGroupSeeder::class,
            CustomerSeeder::class,
          //  AttributeSeeder::class,
            FilterSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            SaleSeeder::class,
            VoucherSeeder::class,
            ThemeSeeder::class
        ]);
    }
}
