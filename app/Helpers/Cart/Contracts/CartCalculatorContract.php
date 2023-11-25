<?php

namespace App\Helpers\Cart\Contracts;

use Illuminate\Database\Eloquent\Model;

interface CartCalculatorContract
{

    public function calculate();

    public function getCouponModel(): ?Model;


}
