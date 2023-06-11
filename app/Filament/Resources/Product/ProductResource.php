<?php

namespace App\Filament\Resources\Product;

use App\Filament\Resources\Product\ProductResource\Pages;
use App\Filament\Resources\Product\ProductResource\RelationManagers;
use App\Models\Product\Product;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('attribute_group_id'),
                Forms\Components\TextInput::make('parent_id'),
                Forms\Components\TextInput::make('sku')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('url_key')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('quantity')
                    ->required(),
                Forms\Components\TextInput::make('popularity')
                    ->required(),
                Forms\Components\TextInput::make('view_count')
                    ->required(),
                Forms\Components\Toggle::make('featured')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('visible_individually')
                    ->required(),
                Forms\Components\TextInput::make('base_price')
                    ->required(),
                Forms\Components\TextInput::make('commission_percent')
                    ->required(),
                Forms\Components\TextInput::make('commission_amount')
                    ->required(),
                Forms\Components\TextInput::make('hsn_code')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tax_percent')
                    ->required(),
                Forms\Components\TextInput::make('tax_amount')
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->required(),
                Forms\Components\Textarea::make('commissions'),
                Forms\Components\TextInput::make('min_range')
                    ->required(),
                Forms\Components\TextInput::make('max_range')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attribute_group_id'),
                Tables\Columns\TextColumn::make('parent_id'),
                Tables\Columns\TextColumn::make('sku'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('url_key'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('popularity'),
                Tables\Columns\TextColumn::make('view_count'),
                Tables\Columns\IconColumn::make('featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\IconColumn::make('visible_individually')
                    ->boolean(),
                Tables\Columns\TextColumn::make('base_price'),
                Tables\Columns\TextColumn::make('commission_percent'),
                Tables\Columns\TextColumn::make('commission_amount'),
                Tables\Columns\TextColumn::make('hsn_code'),
                Tables\Columns\TextColumn::make('tax_percent'),
                Tables\Columns\TextColumn::make('tax_amount'),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\TextColumn::make('commissions'),
                Tables\Columns\TextColumn::make('min_range'),
                Tables\Columns\TextColumn::make('max_range'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
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
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }    
}
