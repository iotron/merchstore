<?php

namespace App\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return parent::toArray($request);

        return [
            'empty' => $this->when(isset($this['empty']),$this['empty']),
            'changed' => $this->when(isset($this['changed']),$this['changed']),
            'coupon' => $this->when(isset($this['coupon']),$this['coupon']),
            'validCoupon' => $this->when(isset($this['coupon']),$this['validCoupon']),
            'subTotal_formatted' => $this['subtotal']->formatted(),
            'tax_formatted' => $this['tax']->formatted(),
            'discount_formatted' => $this['discount']->formatted(),
            'total_formatted' => $this['total']->formatted(),
            'error' => $this->when(!empty($this['error']),$this['error']),
            'products' => $this->when(!empty($this['products']),CartProductResource::collection($this['products'])),
        ];

//        return [
//            'empty' => $this->when(isset($this['empty']),$this['empty']),
//            'changed' => $this->when(isset($this['changed']),$this['changed']),
//            'coupon' => $this->when(isset($this['coupon']),$this['coupon']),
//            'validCoupon' => $this->when(isset($this['coupon']),$this['validCoupon']),
//            'subTotal_formatted' => $this['subtotal']->formatted(),
//            'tax_formatted' => $this['tax']->formatted(),
//            'discount_formatted' => $this['discount']->formatted(),
//            'total_formatted' => $this['total']->formatted(),
//            'error' => $this->when(!empty($this['error']),$this['error']),
//            'products' => $this->when(!empty($this['products']),CartProductResource::collection($this['products'])),
//        ];
    }
}
