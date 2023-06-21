<?php

namespace App\Helpers\FilterHelper;

use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use ReflectionClass;

class AvailableScopes
{

    protected string $modelProperty = 'filterDataScope';
    protected string $dataFileName='';
    protected string $dataPath='';
    protected array $data=[];
    protected ?string $errors = null;
    protected ?string $providedDataScope = null;
    protected object $model;
    protected string $modelName='';
    protected string $scoperPath;

    protected array $scoperInstances=[];

    public bool $isValidDataFile = false;
    public bool $isValidScoper = false;

    public function __construct(object $model,?string $modelFilterDataScoper=null)
    {
       $this->dataPath = __DIR__.DIRECTORY_SEPARATOR.'Data/';
       $this->scoperPath = __DIR__.DIRECTORY_SEPARATOR.'Scopers/';
       $this->providedDataScope = $modelFilterDataScoper;
       $this->model = $model;
       $this->modelName = Str::afterLast(get_class($this->model),'\\');

    }



    public function getFilterScopes(array $allowedFilterList = [])
    {

        if (file_exists($this->scoperPath.$this->modelName.'Scoper.php'))
        {
            $this->scoperInstances = require_once $this->scoperPath.$this->modelName.'Scoper.php';
        }

        if (!empty($allowedFilterList))
        {
            $availableScopes = [];
            foreach ($this->scoperInstances as  $scope => $instance) {
                if (in_array($scope,$allowedFilterList))
                {
                    $availableScopes[$scope] = $instance;
                }
            }
            return $availableScopes;

        }
        return $this->scoperInstances;

    }




    /**
     * Get Custom Or All Filter Options Data
     * @param array $filterList
     * @return array
     */
    public function getFilterOptions(array $filterList = []):array
    {
        $this->analyzeModelScopes();


        if (!empty($filterList))
        {
            $tempBag = [];
            foreach ($filterList as $filterName)
            {
                if (!is_null($filterName)) {
                    // Find the array with matching name
                    foreach ($this->data as $array) {
                        if ($array['name'] == $filterName) {

                            $tempBag[] = $array;
                        }
                    }
                }
            }

            return $tempBag;
        }
        return $this->data;
    }


    protected function analyzeModelScopes(): void
    {
        try {

            // Find DataFileName
            if (is_null($this->providedDataScope))
            {
                $reflection = new ReflectionClass($this->model);

                if ($reflection->hasProperty($this->modelProperty))
                {
                    $property = $reflection->getProperty($this->modelProperty);
                    $this->dataFileName = $property->getValue($this->model);

                }
            }else{
                $this->dataFileName = $this->providedDataScope;
            }


            // Override DataFileName
            if (!file_exists($this->dataPath.$this->dataFileName.'.php'))
            {
                $this->dataFileName = Str::afterLast(get_class($this->model),'\\').'DataScope';
            }

            // CurrentDataFile
            $currentDataFile = $this->dataPath.$this->dataFileName.'.php';

            // Prepare Data
            if (file_exists($currentDataFile))
            {
                $this->data = require_once $currentDataFile;
            }

        }catch (\Throwable $e)
        {
            if ($e->getMessage())
            {
                $this->errors = $e->getMessage();
            }
        }

    }












    public function getScope(?string $name=null)
    {
        return $this->defaultScopeData($name);
    }



    private function defaultScopeData(?string $name=null): array
    {
        $data = [
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

        if ($name !== null) {
            // Find the array with matching name
            foreach ($data as $array) {
                if ($array['name'] === $name) {
                    return [$array];
                }
            }
            return []; // No match found, return an empty array
        }
        return $data;
    }




}
