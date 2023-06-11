<?php

namespace App\Filament\Resources\Attribute\AttributeGroupResource\Pages;

use App\Filament\Resources\Attribute\AttributeGroupResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttributeGroup extends EditRecord
{
    protected static string $resource = AttributeGroupResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
