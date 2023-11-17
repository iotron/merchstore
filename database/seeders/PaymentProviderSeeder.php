<?php

namespace Database\Seeders;

use App\Models\Payment\PaymentProvider;
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

        $razorpay = PaymentProvider::create([
            'name' => 'Razorpay',
            'url' => 'razorpay',
            'status' => true,
        ]);


        $stripe = PaymentProvider::create([
            'name' => 'Stripe',
            'url' => 'stripe',
            'status' => true,
        ]);

    }
}
