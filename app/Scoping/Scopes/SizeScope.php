<?php

namespace App\Scoping\Scopes;

use App\Scoping\Contracts\Scope;
use Illuminate\Database\Eloquent\Builder;

class SizeScope implements Scope
{

    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function apply(Builder $builder, $value): Builder
    {
        return $builder->whereHas('filterOptions',function ($query) use($value) {
            return $query->whereIn('admin_name',explode(',',$value));
        });
    }
}
