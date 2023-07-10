<?php

namespace App\Filament\Resources\Promotion;

use App\Filament\Resources\Promotion\SaleResource\Pages;
use App\Filament\Resources\Promotion\SaleResource\RelationManagers;
use App\Models\Promotion\Sale;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

//    public static function form(Form $form): Form
//    {
//        return $form
//            ->schema([
//                Forms\Components\TextInput::make('name')
//                    ->maxLength(255),
//                Forms\Components\TextInput::make('description')
//                    ->maxLength(255),
//                Forms\Components\DatePicker::make('starts_from'),
//                Forms\Components\DatePicker::make('ends_till'),
//                Forms\Components\Toggle::make('status')
//                    ->required(),
//                Forms\Components\Toggle::make('condition_type')
//                    ->required(),
//                Forms\Components\Textarea::make('conditions'),
//                Forms\Components\Toggle::make('end_other_rules')
//                    ->required(),
//                Forms\Components\TextInput::make('action_type')
//                    ->maxLength(255),
////                Forms\Components\TextInput::make('discount_amount')
////                    ->required(),
//                Forms\Components\TextInput::make('sort_order')
//                    ->required(),
//            ]);
//    }

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
//                Tables\Columns\IconColumn::make('condition_type')
//                    ->boolean(),
//                Tables\Columns\TextColumn::make('conditions'),
                Tables\Columns\IconColumn::make('end_other_rules')
                    ->boolean(),
                Tables\Columns\TextColumn::make('action_type'),
                Tables\Columns\TextColumn::make('discount_amount')->formatStateUsing(function ($state){
                    return $state->formatted();
                }),
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
