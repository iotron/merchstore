<?php

namespace App\Filament\Resources\Promotion;

use App\Filament\Resources\Promotion\SaleResource\Pages;
use App\Models\Promotion\Sale;
use App\Services\Iotron\MoneyService\Money;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationGroup = 'Promotion';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                //                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('starts_from')
                    ->date(),
                Tables\Columns\TextColumn::make('ends_till')
                    ->date(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\IconColumn::make('end_other_rules')
                    ->boolean(),
                Tables\Columns\TextColumn::make('action_type'),
                Tables\Columns\TextColumn::make('discount_amount')->money(Money::defaultCurrency()),
                Tables\Columns\TextColumn::make('sort_order'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\CustomerGroupsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'view' => Pages\ViewSale::route('/{record}'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
