<?php

namespace App\Helpers\Cart;

use App\Helpers\Cart\Services\CartCalculator;
use App\Helpers\Cart\Services\CartService;

class Cart extends CartService
{

//    public function getMeta(): array
//    {
//
//
//        return [
//            'empty' => false,
//            'changed' => true,
//            'coupon' => 'SUMMER50',
//            'validCoupon' => true,
//            'couponModel' => 'CouponModelInstance',
//            'subtotal' =>  150.00,
//            'tax' => 15.00,
//            'discount' => 50.00,
//            'total' => 115.00,
//            'quantity' => 3,
//            'subtotal_formatted' => '$150.00',
//            'tax_formatted' => '$15.00',
//            'discount_formatted' => '$50.00',
//            'total_formatted' => '$115.00',
//            'error' => 'Invalid coupon code',
//            'tickets' => collect([
//                [
//                    'id' => 1,
//                    'name' => 'General Admission',
//                    'quantity' => 2,
//                    'price' => 50.00,
//                    'subtotal' => 100.00,
//                    'subtotal_formatted' => '$100.00'
//                ],
//                [
//                    'id' => 2,
//                    'name' => 'VIP Pass',
//                    'quantity' => 1,
//                    'price' => 100.00,
//                    'subtotal' => 100.00,
//                    'subtotal_formatted' => '$100.00'
//                ]
//            ])
//        ];
//
//
//    }



    public function getMeta(): array
    {
        $cartCalculator = new CartCalculator($this);

        $data = $cartCalculator->calculate();


        return [
            'empty' => $this->isEmpty(),
            'changed' => $this->changed,
            'coupon' => $this->couponCode,
            'validCoupon' => $this->validCoupon,
            'couponModel' => $cartCalculator->getCouponModel(),
            'subtotal' =>  $data['subTotal'],
            'tax' => $data['tax'],
            'discount' => $data['discount'],
            'total' => $data['amount'],
            'quantity' => $this->getTotalQuantity(),
            'subtotal_formatted' => $data['subTotal']->formatted(),
            'tax_formatted' => $data['tax']->formatted(),
            'discount_formatted' => $data['discount']->formatted(),
            'total_formatted' => $data['amount']->formatted(),
            'error' => $this->getErrors(),
            'products' => collect($data['product'])
        ];


    }



}
