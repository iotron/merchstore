<?php

namespace Database\Seeders;

use App\Models\Payment\PaymentProvider;
use App\Services\OrderService\OrderCreationService;
use App\Services\PaymentService\Providers\Razorpay\RazorpayPaymentService;
use App\Services\PaymentService\Providers\Stripe\StripePaymentService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $cashOnDelivery = PaymentProvider::create([
           'name' => 'Cash On Delivery',
           'url' => 'cod',
           'status' => true,
        ]);

        $unCategoriesProvider = PaymentProvider::create([
            'name' => 'General Provider',
            'url' => 'general',
            'service_provider' => OrderCreationService::class,
            'status' => true,
            'is_primary' => false,
            'has_api' => false,
            'desc' => 'This provider only for testing purpose'
        ]);


        $razorpay = PaymentProvider::create([
            'name' => 'Razorpay',
            'url' => 'razorpay',
            'service_provider' => RazorpayPaymentService::class,
            'status' => true,
            'is_primary' => false,
            'has_api' => true,
            'desc' => 'Take Payment and Disburse Payout On Fly'
        ]);


        $stripe = PaymentProvider::create([
            'name' => 'Stripe',
            'url' => 'stripe',
            'service_provider' => StripePaymentService::class,
            'status' => true,
            'is_primary' => true,
            'has_api' => true,
            'desc' => 'Take Payment On Fly'
        ]);

    }
}
