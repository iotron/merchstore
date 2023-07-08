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
            CountrySeeder::class,
            CustomerGroupSeeder::class,
            CustomerSeeder::class,
            AttributeSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class
        ]);
    }
}
