<?php

namespace App\Services\CartService\VoucherService;

use App\Models\Product\Product;
use App\Models\Promotion\Voucher;
use App\Services\MoneyServices\Money;

class VoucherDiscountCalculator
{

    protected Voucher $voucher;
    protected ?string $type = null;
    protected array $meta = [];

    /**
     * @param Voucher $voucher
     * @param array $metaBag
     */
    public function __construct(Voucher $voucher, array $metaBag)
    {
        $this->voucher = $voucher;
        $this->type = $this->voucher->action_type;
        $this->meta = $metaBag;
    }


    public static function make(Voucher $voucher,array $validatedItems = []): static
    {
       return new static($voucher,$validatedItems);
    }

    public function applyDiscount(Product $product)
    {
        match ($this->type) {
            'by_percent' => $this->byProductPercentage($product),
            'by_fixed' => $this->byProductFixed($product),
            'cart_fixed' => $this->byCartFixed($product),
            'cart_percent' => $this->byCartPercentage($product),
            default => new Money(0),
        };
        return $this;
    }


    public function getMeta(): array
    {
        return $this->meta;
    }



    private function byProductPercentage($product): void
    {
        $discount = new Money(0);
        if ($this->meta['items'][$product->sku]['checked']) {

            $discountPercent = (new Money($this->voucher->discount_amount))->divide(100);
            $discount = $discount->addOnce($product->price->multiplyOnce($product->pivot->quantity)->multiplyOnce($discountPercent->getAmount()));
        }
        $this->meta['items'][$product->sku]['discount'] = $discount;
        $this->meta['items'][$product->sku]['discount_formatted'] = $discount->formatted();
        $this->meta['items'][$product->sku]['discount_type'] = 'by_percent';
        //return $discount;
    }

    private function byProductFixed($product): void
    {
        $discount = new Money(0);
        if ($this->meta['items'][$product->sku]['checked']) {
            $discountAmount = $discount->addOnce((new Money($this->voucher->discount_amount))->multiplyOnce($product->pivot->quantity));
            $this->meta['items'][$product->sku]['discount'] = $discountAmount;
            $this->meta['items'][$product->sku]['discount_formatted'] = $discountAmount->formatted();
            $this->meta['items'][$product->sku]['discount_type'] = 'by_fixed';
        }

    }

    private function byCartFixed($product): void
    {
        $discount = new Money(0);
        if ($this->meta['items'][$product->sku]['checked']) {
            $discount->add($this->voucher->discount_amount)->divide($this->meta['totalQuantity']);
            $this->meta['items'][$product->sku]['discount'] = $discount;
            $this->meta['items'][$product->sku]['discount_formatted'] = $discount->formatted();
            $this->meta['items'][$product->sku]['discount_type'] = 'cart_fixed';
        }

    }

    private function byCartPercentage($product)
    {
        $discount = new Money(0);
        if ($this->meta['items'][$product->sku]['checked']) {
            $subTotal = $this->meta['subTotal'];

            $discountPercent = $this->voucher->discount_amount->divide(100);
            $discountAmount = $subTotal->multiply($discountPercent->getValue());
            // Discount Calculation
            $discount->add($discountAmount);

            $this->meta['items'][$product->sku]['discount'] = $discount;
            $this->meta['items'][$product->sku]['discount_formatted'] = $discount->formatted();
            $this->meta['items'][$product->sku]['discount_type'] = 'cart_percent';
        }
    }


}
