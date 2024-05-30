<?php

namespace App\Filament\Resources\Category\ThemeResource\Pages;

use App\Filament\Common\Schema\AdjacencySchema\HasAdjacencyTableSchema;
use App\Filament\Resources\Category\ThemeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListThemes extends ListRecords
{
    use HasAdjacencyTableSchema;

    protected static string $resource = ThemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function table(Table $table): Table
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
