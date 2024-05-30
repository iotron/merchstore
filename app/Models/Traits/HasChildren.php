<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasChildren
{
    // This Parents Scope get collision when use Adjacency List
    //    public function scopeParents(Builder $builder)
    //    {
    //        $builder->whereNull('parent_id');
    //    }

    public function scopeNotParents(Builder $builder)
    {
        $builder->doesntHave('children');
    }
}
