<?php

namespace App\Helpers\Cart\backup\Contracts;

use Illuminate\Database\Eloquent\Model;

interface CartCalculatorContract
{
    public function calculate();

    public function getCouponModel(): ?Model;
}
