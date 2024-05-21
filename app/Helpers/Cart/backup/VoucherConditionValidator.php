<?php

namespace App\Helpers\Cart\backup;

use App\Models\Product\Product;

class VoucherConditionValidator
{
    public function validate(array $condition, array|string|null $attributeValue, Product $product): bool
    {
        dd('Condition Validator', $condition, $attributeValue, $product);
        $isValid = false;

        match ($condition['operator']) {
            '==' => $isValid = $this->validateEqual($condition, $attributeValue),
            '!=' => $isValid = $this->validateNotEqual($condition, $attributeValue),
            '<=' => $isValid = $this->validateLessThanOrEqual($condition, $attributeValue),
            '>' => $isValid = $this->validateGreaterThan($condition, $attributeValue),
            '>=' => $isValid = $this->validateGreaterThanOrEqual($condition, $attributeValue),
            default => throw new \Exception('Invalid operator'),
        };

        return $isValid;
    }

    private function validateEqual()
    {

    }

    private function validateNotEqual()
    {

    }

    private function lessThan()
    {

    }

    private function lessThanOrEqual()
    {

    }

    private function greaterThanOrEqual()
    {

    }
}
