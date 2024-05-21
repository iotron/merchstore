<?php

namespace Database\Seeders;

use App\Models\Payment\PaymentProvider;
use App\Services\PaymentService\Providers\Custom\CustomPaymentService;
use App\Services\PaymentService\Providers\Razorpay\RazorpayPaymentService;
use App\Services\PaymentService\Providers\Stripe\StripePaymentService;
use Illuminate\Database\Seeder;

class PaymentProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $cashOnDeliveryProvider = PaymentProvider::create([
            'name' => PaymentProvider::CODE_OPTIONS[PaymentProvider::CUSTOM],
            'code' => PaymentProvider::CUSTOM,
            'service_provider' => CustomPaymentService::class,
            'status' => true,
            'is_primary' => false,
            'has_api' => false,
            'desc' => 'This provider only for testing purpose',
        ]);

        $razorpay = PaymentProvider::create([
            'name' => PaymentProvider::CODE_OPTIONS[PaymentProvider::RAZORPAY],
            'code' => PaymentProvider::RAZORPAY,
            'service_provider' => RazorpayPaymentService::class,
            'status' => true,
            'is_primary' => true,
            'has_api' => true,
            'desc' => 'Take Payment and Disburse Payout On Fly',
        ]);

        $stripe = PaymentProvider::create([
            'name' => PaymentProvider::CODE_OPTIONS[PaymentProvider::STRIPE],
            'code' => PaymentProvider::STRIPE,
            'service_provider' => StripePaymentService::class,
            'status' => true,
            'is_primary' => false,
            'has_api' => true,
            'desc' => 'Take Payment On Fly',
        ]);

    }
}
