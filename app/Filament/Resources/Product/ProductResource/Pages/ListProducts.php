<?php

namespace App\Filament\Resources\Product\ProductResource\Pages;

use App\Filament\Resources\Product\ProductResource;
use App\Helpers\Money\Money;
use App\Models\Product\Product;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }







    public  function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('thumb')
                    ->circular()
                    ->collection('productDisplay'),
                TextColumn::make('id')->label('ID')->sortable()->searchable()->toggleable()->toggledHiddenByDefault(),
                TextColumn::make('sku')->label('Sku')->searchable(),
                TextColumn::make('type')->label('Type')->sortable()->toggleable(),
                TextColumn::make('attribute_group.code')->label('Group')->sortable()->toggleable()->toggledHiddenByDefault(),
                TextColumn::make('parent_id')->label('Parent')->sortable(),
                TextColumn::make('price')->label('Price')->formatStateUsing(function (Money $state) {
                    return $state->getAmount();
                })->sortable()->toggleable()->toggledHiddenByDefault(),
                TextColumn::make('quantity')->label('Quantity')->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(function ($state){
                        return ucfirst($state);
                    })
                    ->colors([
                    'primary',
                    'danger' => 'draft',
                    'warning' => 'review',
                    'success' => 'published',
                ]),
                TextColumn::make('view_count')->label('Views')->toggleable()->sortable(),
                TextColumn::make('popularity')->label('Popularity')->toggleable()->sortable(),
                TextColumn::make('created_at')
                    ->since()->toggleable(),
                TextColumn::make('updated_at')->label('Modified On')
                    ->since()->toggleable()->toggledHiddenByDefault(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }






}
