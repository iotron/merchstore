<?php

namespace App\Filament\Resources\Category\ThemeResource\Pages;

use App\Filament\Common\Schema\AdjacencySchema\HasAdjacencyFormSchema;
use App\Filament\Resources\Category\ThemeResource;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateTheme extends CreateRecord
{
    use HasAdjacencyFormSchema;
    protected static string $resource = ThemeResource::class;

    protected static bool $canCreateAnother = false;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema($this->getAdjacencyFormSchema());
    }
}
