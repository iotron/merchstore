<?php

namespace App\Filament\Resources\Payment;

use App\Filament\Resources\Payment\PaymentProviderResource\Pages;
use App\Models\Payment\PaymentProvider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Illuminate\Support\Str;

class PaymentProviderResource extends Resource
{
    protected static ?string $model = PaymentProvider::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Providers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Information')
                    ->aside()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->lazy()
                            ->placeholder('Type Payment Provider Name')
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                $set('url', Str::slug($state));
                            })
                            ->hint('Max: 200')
                            ->maxLength(200),

                        Forms\Components\TextInput::make('url')
                            ->required()
                            ->placeholder('Auto filled with name')
                            ->maxLength(255),

                    ]),

                Forms\Components\Section::make('Manage Configuration')
                    ->aside()
                    ->columns(2)
                    ->schema([

                        Forms\Components\Toggle::make('status')
                            ->required(),
                        Forms\Components\Toggle::make('is_primary')
                            ->label(__('Primary'))
                            ->required(),

                    ]),


                Forms\Components\Section::make('Api Configuration')
                    ->aside()
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('Api Key')
                            ->maxLength(255)
                            ->hint('Max : 255 characters')
                            ->placeholder('Type Payment Provider Api Key')
                            ->columnSpanFull()
                            ->nullable(),
                        Forms\Components\TextInput::make('secret')
                            ->label('Api Secret')
                            ->maxLength(255)
                            ->hint('Max : 255 characters')
                            ->placeholder('Type Payment Provider Api Secret')
                            ->columnSpanFull()
                            ->nullable(),

                        Forms\Components\TextInput::make('webhook')
                            ->label('Api WebHook')
                            ->maxLength(255)
                            ->hint('Max : 255 characters')
                            ->placeholder('Type Payment Provider Api WebHook')
                            ->columnSpanFull()
                            ->nullable()
                    ])
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
            'index' => Pages\ListPaymentProviders::route('/'),
            'create' => Pages\CreatePaymentProvider::route('/create'),
            'view' => Pages\ViewPaymentProvider::route('/{record}'),
            'edit' => Pages\EditPaymentProvider::route('/{record}/edit'),
        ];
    }
}
