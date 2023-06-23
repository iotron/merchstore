<?php

namespace App\Helpers\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
class MoneyCast implements CastsAttributes
{

    /**
     * Transform the stored value into a Money instance.
     *
     * @param  mixed  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return Money
     */
    public function get($model, string $key, mixed $value, array $attributes): Money
    {
        return new Money($value);
    }


    /**
     * Transform the attribute's value before persisting it.
     *
     * @param  mixed  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return int[]
     */
    public function set($model, string $key, mixed $value, array $attributes): array
    {
        if ($value instanceof Money) {
            return [$key => (int) $value->getAmount()];
        } else {
            return [$key => (int) $value];
        }
    }
}


