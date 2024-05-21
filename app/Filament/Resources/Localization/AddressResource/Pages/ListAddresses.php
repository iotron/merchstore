<?php

namespace App\Filament\Resources\Localization\AddressResource\Pages;

use App\Filament\Resources\Localization\AddressResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListAddresses extends ListRecords
{
    protected static string $resource = AddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('name')
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Large),
                    Tables\Columns\TextColumn::make('type')->badge(),

                    Tables\Columns\TextColumn::make('addressable_type')
                        ->formatStateUsing(function ($state) {

                        })
                        ->badge(),

                ]),

                Tables\Columns\TextColumn::make('contact'),
                Tables\Columns\TextColumn::make('address_1')->columnSpanFull(),
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('city')->description('City'),
                    Tables\Columns\TextColumn::make('state')->description('State'),
                    Tables\Columns\TextColumn::make('country.name')->description('Country'),
                ]),

            ])
            ->contentGrid([
                'md' => 2,
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
}
