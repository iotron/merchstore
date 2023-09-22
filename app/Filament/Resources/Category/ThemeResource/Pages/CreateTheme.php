<?php

namespace App\Filament\Resources\Category\ThemeResource\Pages;

use App\Filament\Resources\Category\ThemeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTheme extends CreateRecord
{
    protected static string $resource = ThemeResource::class;

    protected static bool $canCreateAnother = false;
}
