<?php

namespace App\Helpers\Cart;

use App\Helpers\Cart\Services\CartCalculator;
use App\Helpers\Cart\Services\CartService;

class Cart extends CartService
{

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
