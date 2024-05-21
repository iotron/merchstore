<?php

namespace App\Helpers\Cart\Services\Voucher;

use App\Helpers\Cart\Contracts\CartServiceContract;
use App\Helpers\Money\Money;
use App\Models\Product\Product;

class ConditionValidator
{
    protected bool $isValid = false;

    protected CartServiceContract $cartService;

    public function __construct(CartServiceContract $cartService)
    {
        $this->cartService = $cartService;
    }

    public function validate(array $condition, mixed $attributeValue, Product $product)
    {
        $operator = $condition['operator'];

        match ($operator) {
            '==' => $this->getEqual($condition, $attributeValue, $product),
            '!=' => $this->getNotEqual($condition, $attributeValue, $product),
            '<=' => $this->getLessThanOrEqual($condition, $attributeValue, $product),
            '>' => $this->getGreaterThan($condition, $attributeValue, $product),
            '>=' => $this->getGreaterThanOrEqual($condition, $attributeValue, $product),
            '{}' => $this->getIn($condition, $attributeValue, $product),
            '!{}' => $this->getNotIn($condition, $attributeValue, $product),
            default => $this->cartService->setError('Invalid operator for voucher condition validation : '.$operator),
        };

        return $this->isValid && empty($this->cartService->getErrors());
    }

    private function getEqual(array $condition, mixed $attributeValue, Product $product): void
    {
        if ($attributeValue instanceof Money) {
            $this->isValid = $attributeValue->sameAs($condition['value']);
        } else {
            // Case Equal
            if (is_array($condition['value']) && ! is_array($attributeValue)) {
                $this->cartService->setError('Condition '.$condition['attribute'].' Attribute type not matched!');
            } else {
                if (is_array($condition['value']) && is_array($attributeValue)) {
                    $this->isValid = ! empty(array_intersect($condition['value'], $attributeValue));
                }

                if (! is_array($condition['value']) && is_array($attributeValue)) {
                    $this->isValid = (\count($attributeValue) == 1 && array_shift($attributeValue) == $condition['value']);
                }

                if (! is_array($condition['value']) && ! is_array($attributeValue)) {
                    $this->isValid = ($attributeValue == $condition['value']);
                }
            }
        }

        if (! $this->isValid) {
            $this->cartService->setError($condition['attribute'].': value '.$condition['value'].' must be equal with '.$attributeValue);
        }
    }

    private function getNotEqual(array $condition, mixed $attributeValue, Product $product): void
    {
        //Case Not Equal
        if ($attributeValue instanceof Money) {
            $this->isValid = ! $attributeValue->sameAs($condition['value']);
        } else {
            if (is_array($condition['value']) && ! is_array($attributeValue)) {
                $this->cartService->setError('Condition '.$condition['attribute'].' Attribute type not matched!');
            } else {
                if (is_array($condition['value']) && is_array($attributeValue)) {
                    $this->isValid = empty(array_intersect($condition['value'], $attributeValue));
                }

                if (! is_array($condition['value']) && is_array($attributeValue)) {
                    $this->isValid = \count($attributeValue) == 1 && array_shift($attributeValue) != $condition['value'];
                }

                if (! is_array($condition['value']) && ! is_array($attributeValue)) {
                    $this->isValid = $attributeValue != $condition['value'];
                }
            }
        }

        if (! $this->isValid) {
            $this->cartService->setError($condition['attribute'].': value '.$condition['value'].' must be not equal with '.$attributeValue);
        }
    }

    private function getLessThanOrEqual(array $condition, mixed $attributeValue, Product $product): void
    {
        if (! is_scalar($attributeValue) && ! ($attributeValue instanceof Money)) {
            $this->cartService->setError($condition['attribute'].' value must be scalar type');
        }
        if (empty($this->cartService->getErrors())) {
            if ($attributeValue instanceof Money) {
                $this->isValid = $attributeValue->getMoney()->lessThanOrEqual((new Money($condition['value']))->getMoney());
            } else {
                $this->isValid = $attributeValue <= $condition['value'];
            }
        }

        if (! $this->isValid) {
            $this->cartService->setError($condition['attribute'].': value '.$attributeValue.' must be less than or equal with conditions  '.$condition['attribute'].': '.$condition['value']);
        }
    }

    /**
     * Greater Than
     */
    private function getGreaterThan(array $condition, mixed $attributeValue, Product $product): void
    {
        if (! ($attributeValue instanceof Money) && ! is_scalar($attributeValue)) {
            $this->cartService->setError($condition['attribute'].' value must be scalar type');
        }
        if ($attributeValue instanceof Money) {
            $this->isValid = $attributeValue->getMoney()->greaterThan((new Money($condition['value']))->getMoney());
        } else {
            $this->isValid = $attributeValue > $condition['value'];
        }

        if (! $this->isValid && empty($this->cartService->getErrors())) {
            $this->cartService->setError($condition['attribute'].': value '.$attributeValue.' must be greater than with conditions  '.$condition['attribute'].': '.$condition['value']);
        }
    }

    private function getGreaterThanOrEqual(array $condition, mixed $attributeValue, Product $product): void
    {
        // Equal Or Greater Than
        if (! ($attributeValue instanceof Money) && ! is_scalar($attributeValue)) {
            $this->cartService->setError($condition['attribute'].' value must be scalar type');
        }
        if ($attributeValue instanceof Money) {
            $this->isValid = $attributeValue->getMoney()->greaterThanOrEqual((new Money($condition['value']))->getMoney());
        } else {
            $this->isValid = $attributeValue >= $condition['value'];
        }
        if (! $this->isValid && empty($this->cartService->getErrors())) {
            $this->cartService->setError($condition['attribute'].': value '.$condition['value'].' and must be equal or greater than item '.$condition['attribute'].': '.$attributeValue);
        }
    }

    private function getIn(array $condition, mixed $attributeValue, Product $product)
    {
        $this->getNotIn($condition, $attributeValue, $product);
    }

    private function getNotIn(array $condition, mixed $attributeValue, Product $product)
    {

        if ($attributeValue instanceof Money && is_array($condition['value'])) {

        } elseif (is_scalar($attributeValue) && is_array($condition['value'])) {
            foreach ($condition['value'] as $item) {
                if (stripos($attributeValue, $item) !== false) {
                    $this->isValid = true;
                }
            }
        } elseif (is_array($condition['value'])) {
            if (! is_array($attributeValue)) {
                $this->cartService->setError($condition['attribute'].' value must be an array');
            }
            $this->isValid = ! empty(array_intersect($condition['value'], $attributeValue));
            if (! $this->isValid) {
                $this->cartService->setError($condition['attribute'].($condition['operator'] = '{}') ? 'must contain ' : 'must not content '.implode(',', $condition['value']).' get '.$attributeValue);
            }
        } else {
            if (is_array($attributeValue)) {
                $this->isValid = self::validateArrayValues($attributeValue, $condition['value']);
                if (! $this->isValid) {
                    $this->cartService->setError($condition['attribute'].' array values validation failed!');
                }
            } else {
                $this->isValid = strpos($attributeValue, $condition['value']) !== false;
                if (! $this->isValid) {
                    $this->cartService->setError($condition['attribute'].($condition['operator'] = '{}') ? 'must contain ' : 'must not content '.implode(',', $condition['value']).' get '.$attributeValue);
                }
            }
        }
    }

    private static function validateArrayValues(array $attributeValue, string $conditionValue): bool
    {
        if (in_array($conditionValue, $attributeValue, true) === true) {
            return true;
        }
        foreach ($attributeValue as $subValue) {
            if (is_array($subValue)) {
                if (self::validateArrayValues($subValue, $conditionValue) === true) {
                    return true;
                }
            }
        }

        return false;
    }
}
