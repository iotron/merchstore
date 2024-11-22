<?php

namespace App\Services\CartService\VoucherService;

use App\Models\Product\Product;
use App\Services\MoneyServices\Money;

class VoucherConditionValidator
{

    protected array $errors = [];
    protected bool $isValid = false;




    public static function make(): static
    {
        return new static();
    }


    public function validate(array $condition, $attributeValue):bool
    {
        $operator = $condition['operator'];
        match ($operator) {
            '==' => $this->getEqual($condition, $attributeValue),
            '!=' => $this->getNotEqual($condition, $attributeValue),
            '<=' => $this->getLessThanOrEqual($condition, $attributeValue),
            '>' => $this->getGreaterThan($condition, $attributeValue),
            '>=' => $this->getGreaterThanOrEqual($condition, $attributeValue),
            '{}' => $this->getIn($condition, $attributeValue),
            '!{}' => $this->getNotIn($condition, $attributeValue),
            default => $this->errors [] = 'Invalid operator for voucher condition validation : '.$operator,
        };

        return $this->isValid && empty($this->errors);
    }


    public function getError(): array
    {
        return $this->errors;
    }




    private function getEqual(array $condition, $attributeValue): void
    {
        // Case Equal

        if ($attributeValue instanceof Money)
        {
            $this->isValid = $attributeValue->sameAs($condition['value']);
        }else{
            if (is_array($condition['value']) && ! is_array($attributeValue)) {
                $this->errors[] = 'Condition '.$condition['attribute'].' Attribute type not matched!';
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
            $this->errors [] = $condition['attribute'].': value '.$condition['value'].' must be equal with '.$attributeValue;
        }
    }

    private function getNotEqual(array $condition, $attributeValue): void
    {
        //Case Not Equal
        if ($attributeValue instanceof Money) {
            $this->isValid = ! $attributeValue->sameAs($condition['value']);
        }else{
            if (is_array($condition['value']) && ! is_array($attributeValue)) {
                $this->errors [] = 'Condition '.$condition['attribute'].' Attribute type not matched!';
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
            $this->errors [] = $condition['attribute'].': value '.$condition['value'].' must be not equal with '.$attributeValue;
        }
    }

    private function getLessThanOrEqual(array $condition, $attributeValue): void
    {
        // Case Less Than Or Equal
        if (! is_scalar($attributeValue) && ! ($attributeValue instanceof Money)) {
            $this->errors[] = $condition['attribute'].' value must be scalar type';
        }
        if (empty($this->errors)) {
            if ($attributeValue instanceof Money) {
                $this->isValid = $attributeValue->lessThanOrEqual((new Money($condition['value'])));
            } else {
                $this->isValid = $attributeValue <= $condition['value'];
            }
        }

        if (! $this->isValid) {
            $this->errors [] = $condition['attribute'].': value '.$attributeValue.' must be less than or equal with conditions  '.$condition['attribute'].': '.$condition['value'];
        }
    }

    private function getGreaterThan(array $condition, $attributeValue): void
    {
        //Greater Than
        if (! ($attributeValue instanceof Money) && ! is_scalar($attributeValue)) {
            $this->errors[] = $condition['attribute'].' value must be scalar type';
        }

        if ($attributeValue instanceof Money) {
            $this->isValid = $attributeValue->greaterThan((new Money($condition['value'])));
        } else {
            $this->isValid = $attributeValue > $condition['value'];
        }


        if (! $this->isValid && empty($this->errors)) {
            $this->errors [] = $condition['attribute'].': value '.$attributeValue.' must be greater than with conditions  '.$condition['attribute'].': '.$condition['value'];
        }
    }

    private function getGreaterThanOrEqual(array $condition, $attributeValue): void
    {
        // Equal Or Greater Than
        if (! ($attributeValue instanceof Money) && ! is_scalar($attributeValue)) {
            $this->errors [] = $condition['attribute'].' value must be scalar type';
        }

        if ($attributeValue instanceof Money) {
            $this->isValid = $attributeValue->greaterThanOrEqual((new Money($condition['value'])));
        } else {
            $this->isValid = $attributeValue >= $condition['value'];
        }

        if (! $this->isValid && empty($this->errors)) {
            $this->errors [] = $condition['attribute'].': value '.$condition['value'].' and must be equal or greater than item '.$condition['attribute'].': '.$attributeValue;
        }
    }

    private function getIn(array $condition, $attributeValue): void
    {
        $this->getNotIn($condition, $attributeValue);
    }

    private function getNotIn(array $condition, $attributeValue): void
    {
        if (is_scalar($attributeValue) && is_array($condition['value'])) {
            foreach ($condition['value'] as $item) {
                if (stripos($attributeValue, $item) !== false) {
                    $this->isValid = true;
                }
            }
        } elseif (is_array($condition['value'])) {
            if (! is_array($attributeValue)) {
                $this->errors[] = $condition['attribute'].' value must be an array';
            }
            $this->isValid = ! empty(array_intersect($condition['value'], $attributeValue));
            if (! $this->isValid) {
                $this->errors[] = $condition['attribute'].($condition['operator'] = '{}') ? 'must contain ' : 'must not content '.implode(',', $condition['value']).' get '.$attributeValue;
            }
        } else {
            if (is_array($attributeValue)) {
                $this->isValid = self::validateArrayValues($attributeValue, $condition['value']);
                if (! $this->isValid) {
                    $this->errors[] = $condition['attribute'].' array values validation failed!';
                }
            } else {
                $this->isValid = strpos($attributeValue, $condition['value']) !== false;
                if (! $this->isValid) {
                    $this->errors[] = $condition['attribute'].($condition['operator'] = '{}') ? 'must contain ' : 'must not content '.implode(',', $condition['value']).' get '.$attributeValue;
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
