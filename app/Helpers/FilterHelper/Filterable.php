<?php

namespace App\Helpers\FilterHelper;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    /**
     * Get available filter scopes.
     *
     * @param array        $allowedFilters
     * @param string|null  $modelFilterDataScoper
     * @return array
     */
    public static function availableScopes(array $allowedFilters = [], ?string $modelFilterDataScoper = null): array
    {
        $modelInstance = new self();
        $availableScopes = new AvailableScopes($modelInstance, $modelFilterDataScoper);
        return $availableScopes->getFilterScopes($allowedFilters);
    }

    /**
     * Get filter scopes with options.
     *
     * @param array   $allowedFilters
     * @param string|null  $dataScope
     * @return array
     */
    public static function availableScopesWithOption(array $allowedFilters = [], ?string $dataScope = null): array
    {
        $modelInstance = new self();
        $availableScopes = new AvailableScopes($modelInstance, $dataScope);

        return $availableScopes->getFilterOptions($allowedFilters);
    }


    /**
     * @param $filters
     * @param array $allowedFilters
     * @param string|null $modelFilterDataScoper
     * @return mixed
     */
    public static function filter($filters, array $allowedFilters = [], ?string $modelFilterDataScoper = null)
    {
        return (new static)->newQuery()->filter($filters,$allowedFilters,$modelFilterDataScoper);
    }

    /**
     * Filter the model query based on the provided filters.
     *
     * @param Builder $query
     * @param array|Request $filters
     * @param array $allowedFilters
     * @param string|null $modelFilterDataScoper
     * @return Builder
     */
    public function scopeFilter($query, array|Request $filters, array $allowedFilters = [], ?string $modelFilterDataScoper = null): Builder
    {
        $filters = self::ifRequest($filters);

        $scopeBag = [];
        $availableScopes = self::availableScopes($allowedFilters, $modelFilterDataScoper);
        if (!empty($filters))
        {
            foreach ($filters as $key => $value) {
                $lowercaseKey = strtolower($key);
                if (isset($availableScopes[$lowercaseKey])) {
                    $scope = $availableScopes[$lowercaseKey];
                    $query = $scope->apply($query, $value);
                }
            }
        }

        return $query;
    }



    /**
     * Check if direct request provided and return filters.
     *
     * @param  array|Request  $filters
     * @return array
     */
    protected static function ifRequest(array|Request $filters): array
    {
        if ($filters instanceof Request) {
            return $filters->input('filter', []);
        }

        return $filters;
    }




//    /**
//     * Check if a key is filterable.
//     *
//     * @param  string  $key
//     * @return bool
//     */
//    protected function isFilterable($key): bool
//    {
//        return in_array($key, $this->filterable ?? []);
//    }


}
