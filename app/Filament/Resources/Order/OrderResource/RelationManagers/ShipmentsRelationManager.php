<?php

namespace App\Filament\Resources\Order\OrderResource\RelationManagers;

use App\Models\Order\OrderShipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShipmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'shipments';


    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('invoice_uid')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tracking_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_quantity')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options(OrderShipment::StatusOptions)
                    ->required(),
                Forms\Components\Toggle::make('cod')
                    ->required(),

                Forms\Components\Select::make('shippingProvider')
                        ->relationship('shippingProvider','name')
                        ->required(),

                Forms\Components\KeyValue::make('tracking_data'),
                Forms\Components\KeyValue::make('last_update'),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('invoice_uid')
            ->columns([
                Tables\Columns\TextColumn::make('shippingProvider.name'),
                Tables\Columns\TextColumn::make('tracking_id'),
                Tables\Columns\TextColumn::make('total_quantity'),
                Tables\Columns\TextColumn::make('last_update'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('invoice_uid'),
                Tables\Columns\IconColumn::make('cod')->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
