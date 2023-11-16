<?php

namespace App\Scoping\Scopes;

use App\Scoping\Contracts\Scope;
use Illuminate\Database\Eloquent\Builder;

class FilterScope implements Scope
{

    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function apply(Builder $builder, $value): Builder
    {
        $searchValues = is_array(array_values($value)) ? array_values($value) : explode(',', array_values($value));
        return $builder->whereHas('filterOptions',function ($query) use($value,$searchValues) {
            return $query->whereIn('code',$searchValues);
        });
    }
}
