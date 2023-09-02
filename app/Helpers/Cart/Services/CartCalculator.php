<?php

namespace App\Helpers\Cart\Services;

use App\Helpers\Cart\Contracts\CartCalculatorContract;
use App\Helpers\Cart\Contracts\CartServiceContract;
use App\Helpers\Money\Money;
use Illuminate\Database\Eloquent\Model;

class CartCalculator implements CartCalculatorContract
{


    private CartServiceContract $cartService;
    private CartCouponService $couponService;
    protected array $collectionBag=[];

    public function __construct(CartServiceContract $cartService)
    {
        $this->cartService = $cartService;
        $this->couponService = new CartCouponService($cartService);
        if (!is_null($this->cartService->getCouponCode())) {
            $this->couponService->validated($this->cartService->getCouponCode());
        }
    }

    public function getCouponModel(): ?Model
    {
        return $this->couponService->getModel();
    }

    public function calculate():array
    {
        $this->cartService->checkStock();
        $this->cartItemResolver();


        // Calculate All Product Sums...
        $totalBaseAmount = new Money();
        $totalDiscountAmount = new Money();
        $totalTaxAmount = new Money();
        $totalNetAmount = new Money();

        foreach ($this->collectionBag as $product) {
            $totalBaseAmount->add($product['total_base_amount']);
            $totalDiscountAmount->add($product['total_discount_amount']);
            $totalTaxAmount->add($product['total_tax_amount']);
            $totalNetAmount->add($product['net_total']);
        }
        // Prepare For Meta
        // return Data
        return [
            'subTotal' => $totalBaseAmount,
            'discount' => $totalDiscountAmount,
            'tax' => $totalTaxAmount,
            'amount' => $totalNetAmount,
            'product' => $this->collectionBag,
        ];

    }


    protected function cartItemResolver(): void
    {

        foreach ($this->cartService->products() as $product) {
            // Calculate SubTotal Each Product
            $subTotal = $product->base_price->multiplyOnce($product->pivot->quantity);

            // Calculate Discount From Voucher For Each Product
            $totalDiscount = ($this->couponService->isValid() && !is_null($this->couponService->getModel())) ?
                $this->couponService->getModel()->voucher->discount_amount->multiplyOnce($product->pivot->quantity) :
                new Money(0);
            // Calculate Tax Each Product
            $totalTax = ($product->tax_percent > 0) ? $product->tax_amount->multiplyOnce($product->pivot->quantity) : new Money(0);
            // Calculate Net Total Each Product
            $netTotal = $subTotal->addOnce($totalTax)->subOnce($totalDiscount);

            // Fill Array Into Bag
            $this->collectionBag [] = [
                'id' => $product->id,
                'pivot_quantity' => $product->pivot->quantity,
                'name' => $product->name,
                'has_tax' => !empty($product->tax_percent),
                'base_price' => $product->base_price,
                'total_base_amount' => $subTotal,
                'discount_amount' => (!is_null($this->couponService->getModel()) && $this->couponService->isValid()) ? $this->couponService->getModel()->discount_amount : (new Money()),
                'total_discount_amount' => $totalDiscount,
                'tax_amount' => $product->tax_amount,
                'total_tax_amount' => $totalTax,
                'net_total' => $netTotal,
                'product' => $product
            ];
        }
    }



}
