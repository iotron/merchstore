<?php

namespace Database\Seeders;

use App\Helpers\Cart\Cart;
use App\Models\Customer\Customer;
use App\Models\Order\Order;
use App\Models\Payment\PaymentProvider;
use App\Models\Product\Product;
use App\Services\OrderService\OrderCreationService;
use App\Services\PaymentService\PaymentService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(PaymentService $paymentService): void
    {
        // Fake Order Seeder


        $nonReturnableProducts = Product::where([
            ['type','=',Product::SIMPLE],
            ['status','=',Product::PUBLISHED],
            ['is_returnable','=',false]
        ])->limit(10)->get();

        $returnableProducts = Product::where([
            ['type','=',Product::SIMPLE],
            ['status','=',Product::PUBLISHED],
            ['is_returnable','=',true]
        ])->limit(10)->get();



        $customer = Customer::with('addresses')->firstWhere('email','customer@example.com');
        foreach ($nonReturnableProducts as $product)
        {


            $cart = new Cart($customer);

            $cart->add($product->id,rand(2,20));


            if (fake()->boolean)
            {
                // Add More Products
                $cart->add($nonReturnableProducts->where('id','!=',$product->id)->random()->first()->id,rand(2,20));
            }

//            if (fake()->boolean)
//            {
//                // Add Returnable Product
//                $cart->add($returnableProducts->random()->first()->id,rand(2,20));
//            }

            // Add Returnable Product
            $cart->add($returnableProducts->random()->first()->id,rand(2,20));



            $paymentProvider = $paymentService->provider(PaymentProvider::CUSTOM)->getProvider();

            $orderCreationService = new OrderCreationService($paymentProvider,$cart);

            $shippingIsBilling = fake()->boolean;

            if ($shippingIsBilling)
            {
                $shippingAddress = $customer->addresses->random()->first();
                $billingAddress = $customer->addresses->random()->first();
            }else{

                // Get two different random addresses
                $shippingAddressIndex = rand(0, $customer->addresses->count() - 1);
                $billingAddressIndex = rand(0, $customer->addresses->count() - 1);

                $shippingAddress = $customer->addresses->get($shippingAddressIndex);
                $billingAddress = $customer->addresses->get($billingAddressIndex);
            }

            $uuid = $this->generateUniqueID();
            $orderCreationService->checkout($uuid,$shippingAddress,$billingAddress);


        }

    }


    protected function generateUniqueID() {
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Custom character set
        $prefix = now()->format('dHis'); // Timestamp prefix
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $random = substr(str_shuffle(str_repeat($characters, 4)), 0, 4);
            $id = $prefix . $random;
            $attempt++;
        } while (Order::where('uuid', $id)->exists() && $attempt < $maxAttempts);

        if ($attempt == $maxAttempts) {
            //throw new Exception('Unable to generate unique ID');
            return null;
        }

        return $id;
    }













}
