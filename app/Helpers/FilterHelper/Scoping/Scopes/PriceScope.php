<?php

namespace App\Helpers\FilterHelper\Scoping\Scopes;

use App\Helpers\FilterHelper\Scoping\Contracts\Scope;
use Illuminate\Database\Eloquent\Builder;

class PriceScope implements Scope
{



    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function apply(Builder $builder, $value): Builder
    {
        if (is_string($value) && str_contains($value,','))
        {
            $value = explode(',',$value);
        }


        if (is_array($value))
        {
            return $builder->whereBetween('price',$value);
        }else{
            return $builder->where('price',$value);
        }

    }



}
