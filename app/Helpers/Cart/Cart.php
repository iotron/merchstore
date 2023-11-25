<?php

namespace App\Helpers\Cart;

use App\Helpers\Cart\Services\CartCalculator;
use App\Helpers\Cart\Services\CartService;

class Cart extends CartService
{

    public function getMeta(): array
    {
        $cartCalculator = new CartCalculator($this);
        $cartCalculator->calculate();



        return  [
            'empty' => $this->isEmpty(),
            'changed' => $this->changed,
            'coupon' => $this->couponCode,
            'validCoupon' => $this->validCoupon,
            'couponModel' => $this->couponModel,
            'subtotal' =>  $this->subTotal,
            'tax' => $this->taxTotal,
            'discount' => !is_null($this->discountTotal) ? $this->discountTotal : null,
            'total' => $this->total,
            'quantity' => $this->getTotalQuantity(),
            'subtotal_formatted' => $this->subTotal->formatted(),
            'tax_formatted' => $this->taxTotal->formatted(),
            'discount_formatted' => !is_null($this->discountTotal) ? $this->discountTotal->formatted() :null,
            'total_formatted' => $this->total->formatted(),
            'error' => $this->getErrors(),
            'products' => $this->meta
        ];



    }


    public function getMetaData(): array
    {

        //        if ($this->couponCode)
//        {
//            $this->meta = $result;
//            $this->meta['subTotal'] = $this->meta['subtotal'];
//            $this->meta['totalQuantity'] = $this->meta['quantity'];
////            $voucherService = new VoucherCartService($this->couponCode,$this);
////            dd($voucherService->validate());
////            if ($voucherService->validate())
////            {
////                return true;
////            }
//        }
//
//        return $result;



        return $this->meta;


    }


//    public function resolveCartMeta()
//    {
//        $resolver = new CartResolver($this);
//        $resolver->resolve();
//    }


}
