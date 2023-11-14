<?php

namespace App\Helpers\ProductHelper\Support\Attributes;

//use App\Models\Attribute\AttributeGroup;
use App\Models\Filter\FilterGroup;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;


class AttributeHelper
{


    /**
     * @param $id
     * @return array
     */
    public function getProductAttributes($id): array
    {
        $groups = FilterGroup::where('id', $id)
            ->with('attributes.options')->get();

        return $groups->map(function ($group, $key) {
            // Filterable Only
            $attributeBag = $group->attributes->map(function ($item, $key) {
                $optionBag = $item->options->mapWithKeys(function ($item, $key) {
                    return [$item['display_name'] => $item['display_name']];
                })->toArray();

                return Select::make('filter_attributes.'.$item->display_name)
                    ->options($optionBag)
                    ->required($item->is_required)
                    ->helperText($item->desc);
            })->toArray();

            return
                Section::make('Filterable Attributes')->schema($attributeBag)->columns(2);
        })->toArray();
    }



}
