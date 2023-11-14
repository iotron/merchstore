<?php

namespace App\Scoping\Scopes;

use App\Scoping\Contracts\Scope;
use Illuminate\Database\Eloquent\Builder;

class ThemeScope implements Scope
{
    public function apply(Builder $builder, $value): Builder
    {
        return $builder->whereHas('themes', function ($builder) use ($value) {
            $builder->whereIn('url', explode(',', $value));
        });
    }
}
