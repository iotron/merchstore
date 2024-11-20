<?php

namespace App\Services\CartService\VoucherService;

use App\Models\Promotion\Voucher;

class VoucherService
{

    protected ?Voucher $voucher = null;

    protected array $conditions = [];
    public function __construct(?Voucher $voucher = null)
    {
        $this->voucher = $voucher;
        $this->conditions = $this->voucher->conditions;
    }


    public function getDiscount():int
    {
        return 0;
    }





}
