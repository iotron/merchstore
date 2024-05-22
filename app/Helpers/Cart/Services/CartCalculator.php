<?php

namespace App\Helpers\Cart\Services;

use App\Helpers\Cart\Contracts\CartCalculatorContract;
use App\Helpers\Cart\Contracts\CartServiceContract;
use App\Helpers\Cart\Services\Voucher\VoucherValidator;

use App\Services\Iotron\MoneyService\Money;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CartCalculator implements CartCalculatorContract
{
    private ?CartServiceContract $cartService = null;

    private ?CartCouponService $couponService = null;

    protected array $collectionBag = [];

    protected bool $hasCoupon = false;

    protected bool $validCoupon = false;

    protected ?Collection $productCollection = null;

    public function __construct(CartServiceContract $cartService)
    {
        $this->cartService = $cartService;
        $this->cartService->checkStock();
        $this->productCollection = $this->cartService->products();
        $this->hasCoupon = ! is_null($this->cartService->getCouponCode());
        if ($this->hasCoupon) {
            $this->couponService = new CartCouponService($cartService);
            if (! is_null($this->cartService->getCouponCode())) {
                if ($this->couponService->validated($this->cartService->getCouponCode())) {
                    $this->validCoupon = true;
                }
            }
        }
    }

    public function getCouponModel(): ?Model
    {
        return ! is_null($this->couponService) ? $this->couponService->getModel() : null;
    }

    public function calculate()
    {

        // Base Calculation
        $subTotal = new Money(0.00);
        $totalTax = new Money(0.00);
        // Load Product Infos
        $collectionBag = $this->productCollection->mapWithKeys(function ($product) use ($subTotal, $totalTax) {
            // subtotal
            $productSubTotal = new Money(0.00);
            $productSubTotal->add($product->base_price->multiplyOnce($product->pivot->quantity));
            $subTotal->add($productSubTotal);
            // tax
            $productTotalTax = new Money(0.00);
            if ($product->tax_percent > 0) {
                $productTotalTax->add($product->base_price->multiplyOnce($product->pivot->quantity));
                $totalTax->add($productTotalTax);
            }

            // return data
            return [
                $product->id => [
                    'id' => $product->id,
                    'pivot_quantity' => $product->pivot->quantity,
                    'name' => $product->name,
                    'has_tax' => ! empty($product->tax_percent),
                    'base_price' => $product->base_price->getAmount(),
                    'base_price_formatted' => $product->base_price->formatted(),
                    'total_base_amount' => $productSubTotal->getAmount(),
                    'total_base_amount_formatted' => $productSubTotal->formatted(),
                    'tax_amount' => $product->tax_amount->getAmount(),
                    'tax_amount_formatted' => $product->tax_amount->formatted(),
                    'total_tax_amount' => $productTotalTax->getAmount(),
                    'total_tax_amount_formatted' => $productTotalTax->formatted(),
                    'net_total' => $productSubTotal->addOnce($productTotalTax)->getAmount(),
                    'net_total_formatted' => $productSubTotal->addOnce($productTotalTax)->formatted(),
                    'product' => $product,
                ],
            ];
        });

        $this->cartService->setSubTotal($subTotal);
        $this->cartService->setTaxTotal($totalTax);
        $this->cartService->setTotal($subTotal->addOnce($totalTax));
        $this->cartService->setMeta($collectionBag->toArray());

        $totalDiscount = new Money(0.0);

        // Calculate Discount If Present
        if ($this->hasCoupon & $this->validCoupon) {
            // Validate Voucher Conditions Before Apply Discount
            $voucherValidator = new VoucherValidator($this->cartService);
            if ($voucherValidator->validate()) {
                $result = $voucherValidator->getDiscount();
                if ($result->count()) {
                    // Here we can calculate Discount For Products
                    $cartMeta = $this->cartService->getAttribute('meta');

                    foreach ($cartMeta as $item) {
                        $totalDiscount->add($item['discount']);
                    }
                }
            }
        }

        if ($totalDiscount->getAmount() > 0) {
            $this->cartService->setAttribute('discountTotal', $totalDiscount);
            $totalWillBe = new Money(0.0);
            $totalWillBe->add($subTotal)->add($totalTax)->subtract($totalDiscount);
            $this->cartService->setAttribute('total', $totalWillBe);
        }

    }
}
