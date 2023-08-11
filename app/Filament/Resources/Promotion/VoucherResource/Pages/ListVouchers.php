<?php

namespace App\Filament\Resources\Promotion\VoucherResource\Pages;

use App\Filament\Resources\Promotion\VoucherResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class ListVouchers extends ListRecords
{
    protected static string $resource = VoucherResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }



    public  function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),

                TextColumn::make('starts_from')
                    ->date(),
                TextColumn::make('ends_till')
                    ->date(),
                IconColumn::make('status')
                    ->boolean(),

                TextColumn::make('times_used'),
//                IconColumn::make('condition_type')
//                    ->boolean(),
//
//                IconColumn::make('end_other_rules')
//                    ->boolean(),
//                TextColumn::make('action_type'),
                TextColumn::make('discount_amount')->formatStateUsing(function ($state){
                    return $state->formatted();
                }),
//                TextColumn::make('discount_quantity'),
//                TextColumn::make('discount_step'),
//                IconColumn::make('apply_to_shipping')
//                    ->boolean(),
//                IconColumn::make('free_shipping')
//                    ->boolean(),
                TextColumn::make('sort_order'),
                TextColumn::make('created_at')
                    ->dateTime(),
                TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }



}
