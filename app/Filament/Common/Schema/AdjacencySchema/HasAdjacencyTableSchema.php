<?php

namespace App\Filament\Common\Schema\AdjacencySchema;

use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

trait HasAdjacencyTableSchema
{
    public function getAdjacencyTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('parent.name')->badge()
                ->placeholder('No Data')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('name')
                ->searchable()->sortable(),
            Tables\Columns\TextColumn::make('url')
                ->searchable()->sortable(),
            Tables\Columns\IconColumn::make('status')
                ->boolean(),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public function getAdjacencyTableActions(): array
    {
        return [
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
        ];
    }

    public function getAdjacencyTableFilters(): array
    {
        return [
            SelectFilter::make('Category')
                ->relationship('parent', 'name'),
            SelectFilter::make('status')
                ->options([true => 'True', false => 'False']),

            Tables\Filters\TernaryFilter::make('toggle_category_type')
                ->label('Category type')
                ->placeholder('All categories')
                ->trueLabel('Parent Categories Only')
                ->falseLabel('Subcategories Only')
                ->queries(
                    // parent only
                    true: fn (Builder $query) => $query->whereNull('parent_id'),
                    // children only
                    false: fn (Builder $query) => $query->whereNotNull('parent_id'),
                    blank: fn (Builder $query) => $query, // In this example, we do not want to filter the query when it is blank.
                ),

        ];

    }
}
