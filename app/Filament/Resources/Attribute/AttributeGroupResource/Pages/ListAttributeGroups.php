<?php

namespace App\Filament\Resources\Attribute\AttributeGroupResource\Pages;

use App\Filament\Resources\Attribute\AttributeGroupResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttributeGroups extends ListRecords
{
    protected static string $resource = AttributeGroupResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
