<?php

namespace App\Filament\Resources\Category\CategoryResource\Pages;

use App\Filament\Common\Schema\AdjacencySchema\HasAdjacencyTableSchema;
use App\Filament\Resources\Category\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
class ListCategories extends ListRecords
{
    use HasAdjacencyTableSchema;
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * Builds the form used by the Filament resource for creating topics.
     */
    public function form(Form $form): Form
    {
        return $form->schema(array_merge(
            self::$resource::getForm(),
            self::$resource::getParentForm(),
        ));
    }


    public  function table(Table $table): Table
    {
        return $table
            ->columns($this->getAdjacencyTableColumns())
            ->filters($this->getAdjacencyTableFilters())
            ->actions($this->getAdjacencyTableActions())
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }






}
