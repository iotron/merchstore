<?php

namespace App\Helpers\Cart\backup;

use App\Helpers\Cart\Contracts\CartServiceContract;
use App\Helpers\Money\Money;
use App\Models\Product\Product;
use App\Models\Promotion\Voucher;
use App\Models\Promotion\VoucherCode;
use Illuminate\Support\Str;

class VoucherCartService
{
    protected Voucher $voucher;

    protected ?VoucherCode $voucherCode;

    protected array $conditions = [];

    protected CartServiceContract $cartService;

    protected VoucherConditionValidator $conditionValidator;

    public function __construct(string $coupon_code, CartServiceContract $cartService)
    {

        $this->voucherCode = VoucherCode::with('voucher')->firstWhere('code', $coupon_code);

        $this->voucher = $this->voucherCode->voucher;
        $this->conditions = $this->voucher->conditions;
        $this->cartService = $cartService;

        $this->conditionValidator = new VoucherConditionValidator();
    }

    public function validate(): bool
    {
        if (empty($this->conditions)) {
            return true;
        }
        $validConditionCount = 0;

        foreach ($this->conditions as $condition) {
            if (! empty($this->cartService->getErrors())) {
                // immediate return if error found
                return false;
            }

            if ($this->voucher->condition_type == Voucher::MATCH_ALL) {
                if (! $this->checkCondition($condition)) {
                    // Return false if Single Condition Failed
                    return false;
                }
                $validConditionCount++;
            }

            if ($this->voucher->condition_type == Voucher::MATCH_ANY) {
                if ($this->checkCondition($condition)) {
                    $validConditionCount++;

                    return true;
                }

                return false;
            }
        }

        // Validate Valid Condition Count
        if ($this->voucher->condition_type == Voucher::MATCH_ALL) {
            return $validConditionCount == count($this->conditions);
        } else {
            return $validConditionCount > 0;
        }

    }

    public function checkCondition(array $condition): bool
    {

        $this->cartService->products()->each(function (Product $product) use ($condition) {

            $attributeValue = $this->getAttributeValue($condition, $product);
            if (empty($attributeValue)) {
                $this->cartService->setError($condition['attribute']."'s value not resolved");
            }

            if ($this->conditionValidator->validate($condition, $attributeValue, $product)) {

            }

        });

        dd('sdds');

        return $this->conditionValidator->validate($condition);
    }

    protected function getAttributeValue(array $condition, Product $product)
    {
        $chunks = explode('|', $condition['attribute']);

        $attributeNameChunks = explode('::', $chunks[1]);

        $attributeCode = (\count($attributeNameChunks) > 1) ? $attributeNameChunks[\count($attributeNameChunks) - 1] : $attributeNameChunks[0];

        //dd(current($chunks),$chunks,$attributeCode,$condition);

        // Compare and Validate
        switch (current($chunks)) {
            // $cart+
            case 'cart':
                return $this->getCartAttributeValue($attributeCode);
                break;
                // customer->cart->each->pivot
            case 'cart_item':
                return $this->getCartItemAttributeValue($attributeCode, $product);
                break;
                // customer->cart-each
            case 'product':
                return $this->getProductAttributeValue($attributeCode, $product, $condition);
                break;
            default:
                break;
        }
    }

    protected function getCartAttributeValue(string $attributeCode)
    {
        if (! in_array($attributeCode, ['postcode', 'state', 'country', 'shipping_method', 'payment_method'])) {
            $cartMeta = $this->cartService->getMetaData();

            if (isset($cartMeta[$attributeCode]) && $cartMeta[$attributeCode] instanceof Money) {
                return $cartMeta[$attributeCode]->getAmount();
            } else {
                return $cartMeta[$attributeCode];
            }

            //            if($this->cartService->{$attributeCode} instanceof Money)
            //            {
            //                return $this->cartService->{$attributeCode}->getAmount();
            //            }else{
            //                return $this->cartService->{$attributeCode};
            //            }
        }

        return null;
    }

    protected function getCartItemAttributeValue(string $attributeCode, Product $product)
    {
        return $product->pivot->{$attributeCode};
    }

    protected function getProductAttributeValue(string $attributeCode, Product $product, array $condition)
    {
        if ($attributeCode == 'category_id') {
            return $product->categories()->pluck('id')->toArray();
        } else {
            // Product with Flat Join need
            $value = '';
            $product = $this->populateProduct($product);

            if (isset($product[$attributeCode])) {
                $value = $product[$attributeCode];
            }
            if (isset($product[Str::ucfirst($attributeCode)])) {
                $value = $product[Str::ucfirst($attributeCode)];
            }

            if ($value) {
                $chunk = explode(',', $value);
                if (isset($chunk[1]) && ! empty($chunk[1])) {
                    return $chunk;
                } else {
                    return $chunk[0];
                }
            }
        }
    }

    protected function populateProduct(Product $product)
    {
        $products = $product->toArray();
        $productFlat = $product->flat->toArray();
        $productFilterAttributes = $productFlat['filter_attributes'] ?? [];
        $productPivot = $product->pivot->toArray();

        return array_merge($products, $productFlat, $productFilterAttributes, $productPivot);
    }
}
