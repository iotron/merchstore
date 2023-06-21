<?php

use App\Helpers\FilterHelper\Scoping\Scopes\PriceScope;
use App\Helpers\FilterHelper\Scoping\Scopes\ViewScope;

return [
    'city' => new ViewScope(),
    'time' => new ViewScope(),
    'genre' => new ViewScope(),
    'language' => new ViewScope(),
    'artist' => new ViewScope(),
    'view' => new ViewScope(),
    'type' => new ViewScope(),
    'price' => new PriceScope(),
];
