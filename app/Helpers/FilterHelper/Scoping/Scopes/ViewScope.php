<?php

namespace App\Helpers\FilterHelper\Scoping\Scopes;

use App\Helpers\FilterHelper\Scoping\Contracts\Scope;
use Illuminate\Database\Eloquent\Builder;
class ViewScope implements Scope
{



    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function apply(Builder $builder, $value): Builder
    {
        return $builder->where('views',$value);
    }


}
