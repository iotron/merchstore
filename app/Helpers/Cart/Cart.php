<?php

namespace App\Helpers\Cart;

use App\Helpers\Cart\Services\CartService;

class Cart extends CartService
{

    public function getMeta(): array
    {


        return [
            'empty' => false,
            'changed' => true,
            'coupon' => 'SUMMER50',
            'validCoupon' => true,
            'couponModel' => 'CouponModelInstance',
            'subtotal' =>  150.00,
            'tax' => 15.00,
            'discount' => 50.00,
            'total' => 115.00,
            'quantity' => 3,
            'subtotal_formatted' => '$150.00',
            'tax_formatted' => '$15.00',
            'discount_formatted' => '$50.00',
            'total_formatted' => '$115.00',
            'error' => 'Invalid coupon code',
            'tickets' => collect([
                [
                    'id' => 1,
                    'name' => 'General Admission',
                    'quantity' => 2,
                    'price' => 50.00,
                    'subtotal' => 100.00,
                    'subtotal_formatted' => '$100.00'
                ],
                [
                    'id' => 2,
                    'name' => 'VIP Pass',
                    'quantity' => 1,
                    'price' => 100.00,
                    'subtotal' => 100.00,
                    'subtotal_formatted' => '$100.00'
                ]
            ])
        ];


    }

}
