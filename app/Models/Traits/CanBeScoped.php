<?php

namespace App\Models\Traits;

use App\Scoping\Scoper;
use Illuminate\Database\Eloquent\Builder;

trait CanBeScoped
{
    public function scopeWithScopes(Builder $builder, $scopes = []): Builder
    {
        return (new Scoper(request()))->apply($builder, $scopes);
    }
}
