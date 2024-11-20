<?php

namespace App\Services\CartService\Support;

use App\Models\Promotion\VoucherCode;
use App\Services\CartService\VoucherService\VoucherService;
use Illuminate\Database\Eloquent\Collection;

class CartCalculationService
{

    protected Collection $items;
    protected ?string $couponCode = null;
    protected ?VoucherCode $couponModel = null;
    protected array $itemBag = [];

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

    public function get()
    {
        $this->calculateCartItems();
    }



    /**
     * Calculation
     */


    protected function calculateCartItems()
    {
        foreach ($this->items as $item)
        {
            // Check Each Item and Fill in Array


            $this->itemBag [] = [
                'id' => $item->id,
                'item' => $item
            ];



            if(!is_null($this->couponModel))
            {
                $this->couponModel->loadMissing('voucher');
                $voucherService = new VoucherService($this->couponModel->voucher);
            }



        }

        dd($this);
    }


















}
