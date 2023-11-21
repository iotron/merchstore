<?php

namespace App\Filament\Resources\Shipping;

use App\Filament\Resources\Shipping\ShippingProviderResource\Pages;
use App\Filament\Resources\Shipping\ShippingProviderResource\RelationManagers;
use App\Models\Shipping\ShippingProvider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ShippingProviderResource extends Resource
{
    protected static ?string $model = ShippingProvider::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Providers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->placeholder('Type Provider Name')
                    ->lazy()
                    ->afterStateUpdated(function ($state,Set $set){
                        $set('code',Str::slug($state));
                    })
                    ->hint('Max: 100')
                    ->maxLength(100),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->placeholder('Type Provider Code')
                    ->hint('Max: 100')
                    ->maxLength(100),
                Forms\Components\Select::make('service_provider')
                    ->columnSpanFull()
                    ->placeholder('Select a service provider')
                    ->options(ShippingProvider::AVAILABLE_PROVIDERS),

                Forms\Components\TextInput::make('key')
                    ->placeholder('Type Provider Api Key/ID')
                    ->hint('Max: 255')
                    ->maxLength(255),
                Forms\Components\TextInput::make('secret')
                    ->placeholder('Type Provider Api Secret')
                    ->hint('Max: 255')
                    ->maxLength(255),
                Forms\Components\TextInput::make('webhook')
                    ->columnSpanFull()
                    ->placeholder('Type Provider Api Webhook Address')
                    ->hint('Max: 255')
                    ->maxLength(255),


                Forms\Components\Grid::make([
                    'md' => 3
                ])->schema([
                    Forms\Components\Toggle::make('is_primary')
                        ->required(),
                    Forms\Components\Toggle::make('has_api')
                        ->required(),
                    Forms\Components\Toggle::make('status')
                        ->required(),
                ]),

                Forms\Components\Textarea::make('desc')
                    ->label('Description')
                    ->maxLength(60000)
                    ->hint('Max: 60K')
                    ->placeholder('Type Provider Details')
                    ->columnSpanFull(),
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
            'index' => Pages\ListShippingProviders::route('/'),
            'create' => Pages\CreateShippingProvider::route('/create'),
            'view' => Pages\ViewShippingProvider::route('/{record}'),
            'edit' => Pages\EditShippingProvider::route('/{record}/edit'),
        ];
    }
}
