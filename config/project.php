<?php

/**
 * This Configuration Holds All Project Related Information
 */
return [



    'product_types' => [
        'simple' => [
            'key' => 'simple',
            'name' => 'Simple',
            'class' => 'App\Helpers\ProductHelper\Support\Types\Simple',
            'sort' => 1,
        ],

        'configurable' => [
            'key' => 'configurable',
            'name' => 'Configurable',
            'class' => 'App\Helpers\ProductHelper\Support\Types\Configurable',
            'sort' => 2,
        ],
        'bundle' => [
            'key' => 'bundle',
            'name' => 'Bundle',
            'class' => 'App\Types\Bundle',
            'sort' => 3,
        ],
    ],





];
