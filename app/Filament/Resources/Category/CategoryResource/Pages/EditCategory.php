<?php

namespace App\Filament\Resources\Category\CategoryResource\Pages;

use App\Filament\Common\Schema\AdjacencySchema\HasAdjacencyFormSchema;
use App\Filament\Resources\Category\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    use HasAdjacencyFormSchema;

    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema($this->getAdjacencyFormSchema());
    }
}
