<?php

namespace App\Filament\Resources\Product;

use App\Filament\Resources\Product\ProductResource\Pages;
use App\Filament\Resources\Product\ProductResource\RelationManagers;
use App\Models\Product\Product;
use Filament\Resources\Resource;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $slug = 'product';

    protected static ?string $navigationGroup = 'Product Management';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    //    public static function form(Form $form): Form
    //    {
    //        return $form
    //            ->schema([
    //                Forms\Components\TextInput::make('attribute_group_id'),
    //                Forms\Components\TextInput::make('parent_id'),
    //                Forms\Components\TextInput::make('sku')
    //                    ->required()
    //                    ->maxLength(255),
    //                Forms\Components\TextInput::make('type')
    //                    ->required()
    //                    ->maxLength(255),
    //                Forms\Components\TextInput::make('name')
    //                    ->required()
    //                    ->maxLength(255),
    //                Forms\Components\TextInput::make('url_key')
    //                    ->required()
    //                    ->maxLength(255),
    //                Forms\Components\TextInput::make('quantity')
    //                    ->required(),
    //                Forms\Components\TextInput::make('popularity')
    //                    ->required(),
    //                Forms\Components\TextInput::make('view_count')
    //                    ->required(),
    //                Forms\Components\Toggle::make('featured')
    //                    ->required(),
    //                Forms\Components\TextInput::make('status')
    //                    ->required()
    //                    ->maxLength(255),
    //                Forms\Components\Toggle::make('visible_individually')
    //                    ->required(),
    //                Forms\Components\TextInput::make('base_price')
    //                    ->required(),
    //                Forms\Components\TextInput::make('commission_percent')
    //                    ->required(),
    //                Forms\Components\TextInput::make('commission_amount')
    //                    ->required(),
    //                Forms\Components\TextInput::make('hsn_code')
    //                    ->maxLength(255),
    //                Forms\Components\TextInput::make('tax_percent')
    //                    ->required(),
    //                Forms\Components\TextInput::make('tax_amount')
    //                    ->required(),
    //                Forms\Components\TextInput::make('price')
    //                    ->required(),
    //                Forms\Components\Textarea::make('commissions'),
    //                Forms\Components\TextInput::make('min_range')
    //                    ->required(),
    //                Forms\Components\TextInput::make('max_range')
    //                    ->required(),
    //            ]);
    //    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AllStocksRelationManager::class,
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
