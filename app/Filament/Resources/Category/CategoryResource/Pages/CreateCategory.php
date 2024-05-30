<?php

namespace App\Filament\Resources\Category\CategoryResource\Pages;

use App\Filament\Common\Schema\AdjacencySchema\HasAdjacencyFormSchema;
use App\Filament\Resources\Category\CategoryResource;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    use HasAdjacencyFormSchema;

    protected static string $resource = CategoryResource::class;

    protected static bool $canCreateAnother = false;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema($this->getAdjacencyFormSchema());
    }
}
