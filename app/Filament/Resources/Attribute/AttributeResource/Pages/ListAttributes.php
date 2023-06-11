<?php

namespace App\Filament\Resources\Attribute\AttributeResource\Pages;

use App\Filament\Resources\Attribute\AttributeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttributes extends ListRecords
{
    protected static string $resource = AttributeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
