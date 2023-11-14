<?php

namespace App\Helpers\Cart\Services\Voucher;

use App\Models\Product\Product;

class VoucherConditionValidator
{

    public function validate(array $condition,array|string|null $attributeValue, Product $product):bool
    {
        dd('Condition Validator',$condition,$attributeValue,$product);
    }


}
