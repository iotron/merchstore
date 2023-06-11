<?php

namespace App\Filament\Resources\Attribute\AttributeResource\Pages;

use App\Filament\Resources\Attribute\AttributeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttribute extends EditRecord
{
    protected static string $resource = AttributeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
