<?php

namespace App\Helpers\Cart\Services\Voucher;

use App\Helpers\Cart\Contracts\CartServiceContract;
use App\Helpers\Money\Money;
use App\Models\Product\Product;

class DiscountCalculator
{
    protected CartServiceContract $cartService;
    protected VoucherValidator $voucherValidator;


    public function __construct(CartServiceContract $cartService,VoucherValidator $voucherValidator)
    {
        $this->cartService = $cartService;
        $this->voucherValidator = $voucherValidator;
    }

    public function byProductPercentage(Product $product): Money
    {
        $discount = new Money(0);
        if ($this->voucherValidator->getStatus($product->sku)) {

            $discountPercent = $this->voucherValidator->getVoucher()->discount_amount->divide(100);
            $discount = $discount->addOnce($product->price->multiply($product->pivot->quantity)->multiply($discountPercent->getAmount()));
        }
        $cartMeta = $this->cartService->getAttribute('meta');
        $cartMeta[$product->id]['discount'] = $discount;
        $cartMeta[$product->id]['discount_formatted'] = $discount->formatted();
        $cartMeta[$product->id]['discount_type'] = 'by_percent';
        $this->cartService->setAttribute('meta',$cartMeta);
        return $discount;
    }

    public function byProductFixed($product)
    {
        $discount = new Money(0);
        if ($this->voucherValidator->getStatus($product->sku))
        {
            // Discount Calculation
            $discount = $discount->add($this->voucherValidator->getVoucher()->discount_amount->multiply($product->pivot->quantity));

            $cartMeta = $this->cartService->getAttribute('meta');
            $cartMeta[$product->id]['discount'] = $discount;
            $cartMeta[$product->id]['discount_formatted'] = $discount->formatted();
            $cartMeta[$product->id]['discount_type'] = 'by_fixed';
            $this->cartService->setAttribute('meta',$cartMeta);
        }
        // Return Discount
        return $discount;
    }

    public function byCartFixed($product): Money
    {
        $discount = new Money(0);
        if ($this->voucherValidator->getStatus($product->sku))
        {
            // Discount Calculation
            $discount = $discount->add($this->voucherValidator->getVoucher()->discount_amount);
            $cartMeta = $this->cartService->getAttribute('meta');
            $cartMeta[$product->id]['discount'] = $discount->divide($this->cartService->products()->count());
            $cartMeta[$product->id]['discount_formatted'] = $discount->formatted();
            $cartMeta[$product->id]['discount_type'] = 'cart_fixed';
            $this->cartService->setAttribute('meta',$cartMeta);
        }
        // Return Discount
        return $discount;
    }

    public function byCartPercentage($product)
    {
        $discount = new Money(0);
        if ($this->voucherValidator->getStatus($product->sku))
        {
            $discountPercent = $this->voucherValidator->getVoucher()->discount_amount->divide(100);
            // Discount Calculation
            $discount = $discount->add($this->cartService->getAttribute('subTotal')->multiply($discountPercent->getAmount()));

            $cartMeta = $this->cartService->getAttribute('meta');
            $cartMeta[$product->id]['discount'] = $discount->divide($this->cartService->products()->count());
            $cartMeta[$product->id]['discount_formatted'] = $discount->formatted();
            $cartMeta[$product->id]['discount_type'] = 'cart_percent';
            $this->cartService->setAttribute('meta',$cartMeta);

        }

        // Return Discount
        return  $discount;
    }
}
