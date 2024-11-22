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

    public function setCoupon(?string $couponCode = null,?VoucherCode $couponModel = null): static
    {
        $this->couponCode = $couponCode;
        $this->couponModel = $couponModel;
        return $this;
    }

    public function get(array $data)
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
            $taxableAmount = $itemPrice->multiplyOnce($item->tax_percent)->divideOnce(100);
            $total = $itemPrice->addOnce($taxableAmount)->multiplyOnce($item->pivot->quantity);
            $this->data ['items'][$item->sku] = [
                'id' => $item->id,
                'price' => Money::format($item->price),
                'quantity' => $item->pivot->quantity,
                'tax_amount' => $taxableAmount->getAmount(),
                'tax_amount_formatted' => $taxableAmount->formatted(),
                'amount' => $total->getValue(),
                'total' => $total->formatted(),
                'item' => $item
            ];
            // Only Update SubTotal value
            $this->data['subTotal'] = $this->data['subTotal']->addOnce($item->price);
        }
    }


    protected function calculateDiscountIfApplicable()
    {
        if(!is_null($this->couponModel) && empty($this->errors))
        {
            $this->couponModel->loadMissing('voucher');
            $this->data = VoucherService::make($this->couponModel->voucher)
                ->setMeta($this->data)
                ->items($this->items)
                ->applyDiscount()
                ->getMeta();


        }


        foreach ($this->data['items'] as $item)
        {
            $this->data['discount'] = $this->data['discount']->add($item['discount']);
            $this->data['tax'] = $this->data['tax']->add($item['tax_amount']);
        }



    }


















}
