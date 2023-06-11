<?php

namespace App\Filament\Resources\Attribute\AttributeGroupResource\Pages;

use App\Filament\Resources\Attribute\AttributeGroupResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAttributeGroup extends CreateRecord
{
    protected static string $resource = AttributeGroupResource::class;

    protected static bool $canCreateAnother = false;
}
