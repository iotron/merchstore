<?php

namespace App\Filament\Resources\Localization;

use App\Filament\Resources\Localization\AddressResource\Pages;
use App\Models\Localization\Address;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class AddressResource extends Resource
{
    protected static ?string $model = Address::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('pickup_location')
                    ->hint('Max: 255')
                    ->maxLength(255),

                Forms\Components\TextInput::make('name')
                    ->hint('Max: 255')
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->hint('Max: 255')
                    ->maxLength(255),

                Forms\Components\TextInput::make('contact')
                    ->hint('Max: 255')
                    ->maxLength(255),

                Forms\Components\TextInput::make('alternate_contact')
                    ->hint('Max: 255')
                    ->maxLength(255),

                Forms\Components\TextInput::make('address_1')
                    ->hint('Max: 255')
                    ->maxLength(255),

                Forms\Components\TextInput::make('address_2')
                    ->hint('Max: 255')
                    ->maxLength(255),

                Forms\Components\TextInput::make('landmark')
                    ->hint('Max: 255')
                    ->maxLength(255),

                Forms\Components\TextInput::make('city')
                    ->hint('Max: 255')
                    ->maxLength(255),

                Forms\Components\TextInput::make('postal_code')
                    ->hint('Max: 255')
                    ->maxLength(255),

                Forms\Components\TextInput::make('state')
                    ->hint('Max: 255')
                    ->maxLength(255),

                Forms\Components\TextInput::make('priority')
                    ->hint('Max: 255')
                    ->maxLength(255),

                Forms\Components\Toggle::make('default'),

                Forms\Components\TextInput::make('country_code')
                    ->hint('Max: 255')
                    ->maxLength(255),

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
            'index' => Pages\ListAddresses::route('/'),
            'create' => Pages\CreateAddress::route('/create'),
            'view' => Pages\ViewAddress::route('/{record}'),
            'edit' => Pages\EditAddress::route('/{record}/edit'),
        ];
    }
}
