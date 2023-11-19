<?php

namespace Database\Seeders;

use App\Models\Shipping\ShippingProvider;
use App\Services\ShippingService\Providers\Manual\ManualShippingService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cashOnDeliveryShippingProvider = ShippingProvider::create([
            'name' => 'Custom',
            'url' => 'custom',
            'service_provider' => ManualShippingService::class,
            'status' => true,
            'is_primary' => false,
            'has_api' => false,
            'desc' => 'This provider only for cod delivery purpose'
        ]);
    }
}
