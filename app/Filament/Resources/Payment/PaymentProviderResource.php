<?php

namespace App\Filament\Resources\Payment;

use App\Filament\Resources\Payment\PaymentProviderResource\Pages;
use App\Filament\Resources\Payment\PaymentProviderResource\RelationManagers;
use App\Models\Payment\PaymentProvider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentProviderResource extends Resource
{
    protected static ?string $model = PaymentProvider::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('url')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('key')
                    ->maxLength(255),
                Forms\Components\TextInput::make('secret')
                    ->maxLength(255),
                Forms\Components\TextInput::make('webhook')
                    ->maxLength(255),
                Forms\Components\TextInput::make('service_provider')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_primary')
                    ->required(),
                Forms\Components\Toggle::make('has_api')
                    ->required(),
                Forms\Components\Toggle::make('status')
                    ->required(),
                Forms\Components\Textarea::make('desc')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('key')
                    ->searchable(),
                Tables\Columns\TextColumn::make('secret')
                    ->searchable(),
                Tables\Columns\TextColumn::make('webhook')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service_provider')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_primary')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_api')
                    ->boolean(),
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
