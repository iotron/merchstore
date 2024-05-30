<?php

namespace App\Filament\Resources\Category\ThemeResource\Pages;

use App\Filament\Common\Schema\AdjacencySchema\HasAdjacencyFormSchema;
use App\Filament\Resources\Category\ThemeResource;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditTheme extends EditRecord
{
    use HasAdjacencyFormSchema;

    protected static string $resource = ThemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema($this->getAdjacencyFormSchema());
    }
}
