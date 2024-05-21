<?php

namespace App\Helpers\Contracts;

use Illuminate\Http\Request;

interface CanBeSortableContract
{
    public function getSortingOptions(): array;

    public function getCurrentSort(Request $request): array;
}
