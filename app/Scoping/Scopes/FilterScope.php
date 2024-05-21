<?php

namespace App\Scoping\Scopes;

use App\Scoping\Contracts\Scope;
use Illuminate\Database\Eloquent\Builder;

class FilterScope implements Scope
{
    public function apply(Builder $builder, $value): Builder
    {
        $searchValues = is_array(array_values($value)) ? array_values($value) : explode(',', array_values($value));

        return $builder->whereHas('filterOptions', function ($query) use ($searchValues) {
            return $query->whereIn('code', $searchValues);
        });
    }
}
