<?php

namespace App\Filament\Resources\Attribute\AttributeResource\Pages;

use App\Filament\Resources\Attribute\AttributeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAttribute extends ViewRecord
{
    protected static string $resource = AttributeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
