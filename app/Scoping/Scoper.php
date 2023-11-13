<?php

namespace App\Scoping;

use App\Scoping\Contracts\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Scoper
{
    protected Request $request;
    protected array $filters=[];

    public function __construct(?array $requestFilter=[])
    {

        $this->filters = $requestFilter ?? [];

    }

    public function apply(Builder $builder, array $scopes): Builder
    {

        foreach ($this->limitScopes($scopes) as $key => $scope) {
            if (! $scope instanceof Scope) {
                continue;
            }

            $scope->apply($builder, $this->filters[$key]);
        }
        //}

        return $builder;
    }


    /**
     * @param array $scopes
     * @return array
     */
    protected function limitScopes(array $scopes):array
    {
        return Arr::only(
            $scopes,
            array_keys($this->filters)
        );
    }

}
