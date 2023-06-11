<?php

namespace App\Filament\Resources\Attribute\AttributeResource\Pages;

use App\Filament\Resources\Attribute\AttributeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAttribute extends CreateRecord
{
    protected static string $resource = AttributeResource::class;
}
