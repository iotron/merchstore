<?php

namespace App\Helpers\Cart\Services\Voucher;

use App\Helpers\Cart\Contracts\CartServiceContract;
use App\Helpers\Cart\Services\CartService;
use App\Helpers\Money\Money;
use App\Models\Product\Product;
use App\Models\Promotion\Voucher;
use App\Models\Promotion\VoucherCode;

class VoucherValidator
{

    protected array $backListCartAttributes = ['postcode', 'state', 'country', 'shipping_method', 'payment_method'];

    protected CartServiceContract $cartService;
    protected ?VoucherCode $voucherCode=null;
    protected ?Voucher $voucher = null;
    protected array $conditions = [];
    private ConditionValidator $conditionValidator;


    public function __construct(CartServiceContract $cartService)
    {
        $this->cartService = $cartService;
        $this->voucherCode = $this->cartService->getCouponModel();
        $this->voucher = $this->voucherCode->voucher;
        $this->conditions = $this->voucher->conditions;
        $this->conditionValidator = new ConditionValidator($this->cartService);
    }

    public function validate():bool
    {
        if (empty($this->conditions)) {
            return true;
        }

        return $this->validateConditions();
//        dd($this,$this->cartService);
    }

    protected function validateConditions():bool
    {
        $validConditionCount = 0;

        foreach ($this->conditions as $condition)
        {
            if (!empty($this->cartService->getErrors()))
            {
                // immediate return if error found
                return false;
            }

            if ($this->voucher->condition_type == Voucher::MATCH_ALL)
            {
                if (!$this->checkCondition($condition))
                {
                    // Return false if Single Condition Failed
                    return false;
                }
                $validConditionCount++;
            }


            if ($this->voucher->condition_type == Voucher::MATCH_ANY)
            {
                if ($this->checkCondition($condition))
                {
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


    public function checkCondition(array $condition):bool
    {

        $this->cartService->products()->each(function (Product $product) use($condition) {

            $attributeValue = $this->getAttributeValue($condition, $product);
            if (empty($attributeValue))
            {
                $this->cartService->setError($condition['attribute']."'s value not resolved");
            }

            if ($this->conditionValidator->validate($condition,$attributeValue,$product))
            {

            }





        });

//        dd('sdds',$this->conditions);
        return true;

        //return $this->conditionValidator->validate($condition);
    }

    protected function getAttributeValue(array $condition, Product $product)
    {
        $chunks = explode('|', $condition['attribute']);

        $attributeNameChunks = explode('::', $chunks[1]);

        $attributeCode = (\count($attributeNameChunks) > 1) ? $attributeNameChunks[\count($attributeNameChunks) - 1] : $attributeNameChunks[0];

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
        if (!in_array($attributeCode, $this->backListCartAttributes))
        {
//            if($this->cartService->getAttribute($attributeCode) instanceof Money)
//            {
//                return $this->cartService->getAttribute($attributeCode)->getAmount();
//            }else{
//                return $this->cartService->getAttribute($attributeCode);
//            }
            return $this->cartService->getAttribute($attributeCode);
        }
    }

    protected function getCartItemAttributeValue(string $attributeCode, Product $product)
    {
        return isset($product->pivot->{$attributeCode}) ? $product->pivot->{$attributeCode} : null;
    }

    protected function getProductAttributeValue(string $attributeCode, Product $product, array $condition)
    {
        if ($attributeCode == 'category_id') {
            return  $product->categories()->pluck('id')->toArray();
        }else{
            $value = null;
            if (isset($product->{$attributeCode}))
            {
                $value = $product->{$attributeCode};
            }elseif (isset($product->{ucfirst($attributeCode)}))
            {
                $value = $product->{ucfirst($attributeCode)};
            }

            if ($value) {

                if (!is_string($value))
                {
                    return $value;
                }else{
                    $chunk = explode(',', $value);

                    if (isset($chunk[1]) && ! empty($chunk[1])) {
                        return $chunk;
                    } else {
                        return $chunk[0];
                    }
                }

            }

        }
        return null;
    }


}
