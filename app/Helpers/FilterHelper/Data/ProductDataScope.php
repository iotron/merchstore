<?php

 return [

     [
         'name' => 'price',
         'multiselect' => true,
         'options' => [
             ['name' => 'price' , 'value' => null],
             ['name' => 'price' , 'value' => []]
         ],
     ],


     [
         'name' => 'city',
         'multiselect' => true,
         // 'options' => City::where('status', true)->get(['name','cities.url as value']),
         'options' => ['name' => 'undefined' , 'value' => null],
     ],
     [
         'name' => 'time',
         'multiselect' => false,
         'options' => [
             ['name' => 'today','value' => 'today'],
             ['name' => 'tomorrow','value' => 'tomorrow'],
             ['name' => 'weekend','value' => 'weekend']
         ],
     ],
     [
         'name' => 'genre',
         'multiselect' => true,
         // 'options' => Genre::where('parent_id', null)->where('status', true)->get(['name','genres.url as value']),
         'options' => ['name' => 'undefined' , 'value' => null],
     ],
     [
         'name' => 'language',
         'multiselect' => true,
//                'options' => collect(FilterOption::where('filter_type', 'Languages')->get('name','filter_options.name as value'))->map(function ($query){
//                    return [
//                        'name' => $query->name,
//                        'value' => $query->name
//                    ];
//                }),
         'options' => ['name' => 'undefined' , 'value' => null],
     ],
     [
         'name' => 'artist',
         'multiselect' => true,
         //'options' => Artist::where('status', true)->get(['name', 'artists.url as value']),
         'options' => ['name' => 'undefined' , 'value' => null],
     ],
     [
         'name' => 'view',
         'multiselect' => false,
         'options' => [
             'name' => 'value',
             'value' => 'value',
         ],
     ],
     [
         'name' => 'type',
         'multiselect' => false,
//                'options' => [
//                    ['name' => Events::ONLINE_PREMIER , 'value' => Events::ONLINE_PREMIER],
//                    ['name' => Events::OUTDOOR, 'value' => Events::OUTDOOR]
//                ],
         'options' => ['name' => 'undefined' , 'value' => null],
     ]
 ];
