<?php

namespace Database\Seeders;

use App\Models\Shipping\ShippingProvider;
use App\Services\ShippingService\Providers\Custom\CustomShippingService;
use App\Services\ShippingService\Providers\Pickrr\PickrrShippingService;
use App\Services\ShippingService\Providers\ShipRocket\ShipRocketShippingService;
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
            'code' => 'custom',
            'service_provider' => CustomShippingService::class,
            'status' => true,
            'is_primary' => true,
            'has_api' => false,
            'desc' => 'This provider only for cod delivery purpose'
        ]);


        $shiprocketShippingProvider = ShippingProvider::create([
            'name' => 'ShipRocket',
            'code' => 'shiprocket',
            'service_provider' => ShipRocketShippingService::class,
            'status' => true,
            'is_primary' => false,
            'has_api' => true,
            'desc' => 'This provider only for delivery purpose'
        ]);


        $pickrrShippingProvider = ShippingProvider::create([
            'name' => 'Pickrr',
            'code' => 'pickrr',
            'service_provider' => PickrrShippingService::class,
            'status' => true,
            'is_primary' => false,
            'has_api' => true,
            'desc' => 'This provider only for delivery purpose'
        ]);


    }
}
