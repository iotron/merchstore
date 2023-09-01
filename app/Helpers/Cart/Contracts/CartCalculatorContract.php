<?php

namespace App\Helpers\Cart\Contracts;

use Illuminate\Database\Eloquent\Model;

interface CartCalculatorContract
{

    public function calculate(): array;

    public function getCouponModel(): ?Model;


}
