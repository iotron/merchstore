<?php

namespace App\Filament\Resources\Attribute\AttributeGroupResource\Pages;

use App\Filament\Resources\Attribute\AttributeGroupResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAttributeGroup extends ViewRecord
{
    protected static string $resource = AttributeGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
