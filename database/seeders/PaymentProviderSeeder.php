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

        $razorpay = PaymentProvider::create([
            'name' => 'Razorpay',
            'url' => config('laravel-razorpay.payment-provider.url'),
            'key' => base64_encode(config('laravel-razorpay.auth.key')),
            'secret' => base64_encode(config('laravel-razorpay.auth.secret')),
            'webhook' => config('laravel-razorpay.auth.webhook'),
            'status' => true,
            'is_primary' => true,
        ]);

        $cash = PaymentProvider::create([
            'name' => 'Cash',
            'url' => PaymentProvider::CASH,
            'key' => null,
            'secret' => null,
            'webhook' => null,
            'status' => true,
            'is_primary' => true,
        ]);



    }
}
