<?php

namespace App\Scoping\Scopes;

use App\Scoping\Contracts\Scope;
use Illuminate\Database\Eloquent\Builder;

class TypeScope implements Scope
{
    public function apply(Builder $builder, $value): Builder
    {
        return $builder->where('type', '=', $value);
    }
}
