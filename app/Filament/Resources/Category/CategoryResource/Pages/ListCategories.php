<?php

namespace App\Filament\Resources\Category\CategoryResource\Pages;

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
            ->columns([
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
            ])
            ->filters([
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




            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }






}
