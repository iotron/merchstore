<?php

namespace App\Services\CartService\Support;

use App\Models\Promotion\VoucherCode;
use App\Services\CartService\VoucherService\VoucherService;
use App\Services\MoneyServices\Money;
use Illuminate\Database\Eloquent\Collection;

class CartCalculationService
{

    protected Collection $items;
    protected ?string $couponCode = null;
    protected ?VoucherCode $couponModel = null;
    protected array $errors = [];
    protected array $data = [];

    public function __construct()
    {
    }


    public static function make(): static
    {
        return new static();
    }


    public function items(Collection $items): static
    {
        $this->items = $items;
        return $this;
    }

    public function setCoupon(VoucherCode $couponModel): static
    {
        $this->couponCode = $couponModel->code;
        $this->couponModel = $couponModel;
        return $this;
    }

    public function get(array $data): array
    {
        $this->data = $data;
        $this->calculateCartItems();
        $this->calculateDiscountIfApplicable();
        return $this->data;
    }



    /**
     * Calculation
     */


    protected function calculateCartItems(): void
    {
        // First Prepare The Calculation Meta
        foreach ($this->items as $item)
        {
            $itemPrice = new Money($item->price);
            $this->data ['items'][$item->sku] = [
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'url' => $item->url,
                'price' => $itemPrice,
                'price_formatted' => $itemPrice->formatted(),
                'tax_percent' => $item->tax_percent,
                'pivot_quantity' => $item->pivot->quantity,
               // 'item' => $item
            ];
            // Only Update SubTotal value
            $this->data['subTotal']->add($itemPrice);
        }
    }


    protected function calculateDiscountIfApplicable(): void
    {
        // Check For Coupon And Discount

        if(!is_null($this->couponModel) && empty($this->errors))
        {
            $this->couponModel->loadMissing('voucher');
            $this->data = VoucherService::make($this->couponModel->voucher)
                ->setMeta($this->data)
                ->items($this->items)
                ->applyDiscount()
                ->getMeta();


        }

        dd($this->data);

        // Here All Preparation Complete And We Can Now Make Total Discount, Tax, and Net Amount
        $totalTax = new Money();

        if ($this->items->count())
        {
            foreach ($this->data['items'] as $sku => $product) {
                // Accumulate discounts
                $this->data['discount']->add($product['discount'] ?? new Money(0));
                // Validate Coupon Code
                $this->data['validCoupon'] = $product['checked'] ?? false;

                // Calculate price after discount
                $productPrice = $product['price'];
                $discount = $product['discount'] ?? new Money(0);
                $productPriceAfterDiscount = $productPrice->subOnce($discount);

                // Check if the product is eligible for tax
                if ($productPriceAfterDiscount->greaterThanOrEqual(new Money(500))) {
                    // Calculate taxable amount for the product
                    $quantity = $product['pivot_quantity'] ?? 1;
                    $taxPercentage = $product['tax_percent'] ?? 0;

                    $taxableAmount = $productPriceAfterDiscount
                        ->multiplyOnce($quantity)
                        ->multiply($taxPercentage)
                        ->divide(100);

                    // Accumulate total tax
                    $totalTax->add($taxableAmount);
                }
            }
        }



        $this->data['tax_amount'] = $totalTax;

        // Calculate Total
        $cartDiscountedSubtotal = $this->data['subTotal']->subOnce($this->data['discount']);
        $this->data['amount'] = $cartDiscountedSubtotal->add($this->data['tax_amount']);



    }


















}
