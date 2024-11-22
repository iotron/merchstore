<?php

namespace App\Services\CartService\VoucherService;

use App\Models\Product\Product;
use App\Models\Promotion\Voucher;
use App\Services\MoneyServices\Money;
use Illuminate\Database\Eloquent\Collection;

class VoucherService
{
    protected array $backListCartAttributes = ['postcode', 'state', 'country', 'shipping_method', 'payment_method'];
    protected ?Voucher $voucher = null;
    protected array $meta = [];
    protected array|Collection $items = [];
    protected array $conditions = [];
    protected array $errors = [];

    public function __construct(?Voucher $voucher = null)
    {
        $this->voucher = $voucher;
        $this->conditions = $this->voucher->conditions;
    }

    public static function make(?Voucher $voucher = null): static
    {
        return new static($voucher);
    }

    public function items(array|Collection $items)
    {
        $this->items = $items;
        return $this;
    }
    public function setMeta(array $meta = []): static
    {
        $this->meta = $meta;
        return $this;
    }

    public function applyDiscount()
    {
        if ($this->validateAllConditions())
        {
            $this->getDiscountAmount();
        }
        return $this;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }


    protected function getDiscountAmount()
    {
        foreach ($this->items as $item)
        {
            $this->meta = VoucherDiscountCalculator::make($this->voucher,$this->meta)
                ->applyDiscount($item)->getMeta();
        }

    }



    protected function validateAllConditions():bool
    {
        if (empty($this->conditions)) {
            return true;
        }

        $validConditionCount = 0;

        foreach ($this->conditions as $condition) {
            if (! empty($this->errors)) {
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


    protected function checkCondition(array $condition): bool
    {
        foreach ($this->items as $item)
        {
            $attributeValue = $this->getAttributeValue($condition, $item);
            $voucherValidator = VoucherConditionValidator::make();



            if (empty($attributeValue)) {
                $this->errors [] = $condition['attribute']."'s value not resolved";
                $this->meta['items'][$item->sku]['checked'] = false;
            } else {
                $this->meta['items'][$item->sku]['checked'] = true;
            }

            if ($voucherValidator->validate($condition,$attributeValue))
            {
                $this->meta['items'][$item->sku]['checked'] = true;
            }else{
                $this->errors [] = $condition['attribute']."'s value not resolved";
                $this->meta['items'][$item->sku]['checked'] = false;
            }

            $this->errors = array_merge($this->errors,$voucherValidator->getError());

//            dump([
//                'value' =>$attributeValue,
//                'condi' => $condition,
//                'check' => empty($attributeValue),
//                'validator' => VoucherConditionValidator::make()->validate($condition,$attributeValue),
//                'meta' => $this->meta
//            ]);
        }

        return empty($this->errors);

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


    private function getCartAttributeValue(string $attributeCode)
    {
        if (! in_array($attributeCode, $this->backListCartAttributes))
        {
            return $this->meta[$attributeCode] ?? null;
        }
        return null;
    }

    private function getCartItemAttributeValue(string $attributeCode, Product $product)
    {
        return isset($product->pivot->{$attributeCode}) ? $product->pivot->{$attributeCode} : null;
    }

    private function getProductAttributeValue(string $attributeCode, Product $product, array $condition)
    {

        if ($attributeCode == 'category_id') {
            return $product->categories()->pluck('id')->toArray();
        } else {

            $value = null;
            if (isset($product->{$attributeCode})) {
                $value = $product->{$attributeCode};
            } elseif (isset($product->{ucfirst($attributeCode)})) {
                $value = $product->{ucfirst($attributeCode)};
            }

            // Special Case
            if (!is_null($value) && $attributeCode == 'quantity')
            {

                $value = $product->availableStocks()->count();

            }

            if ($value) {

                if (! is_string($value)) {
                    return $value;
                } else {
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
