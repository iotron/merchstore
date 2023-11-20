<?php

namespace App\Filament\Resources\Order\OrderResource\Pages;

use App\Filament\Resources\Order\OrderResource;
use App\Helpers\Money\Money;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Filament\Tables;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }



    public  function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tracking_id')
                    ->label(__('Tracking'))
                    ->copyable()
                    ->copyMessage('copied!')
                    ->default('--not found--')
                    ->searchable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('voucher')
                    ->default('--none--')
                    ->searchable(),


                Tables\Columns\TextColumn::make('total')
                    ->formatStateUsing(function ($state){
                        return ($state instanceof Money) ? $state->formatted() : $state;
                    })
                    ->sortable(),


                Tables\Columns\TextColumn::make('customer.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentProvider.name')
                    ->sortable(),
//                Tables\Columns\TextColumn::make('billingAddress.id')
//                    ->numeric()
//                    ->sortable(),
//                Tables\Columns\TextColumn::make('address_id')
//                    ->numeric()
//                    ->sortable(),





                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\IconColumn::make('payment_success')
                    ->boolean(),
//                Tables\Columns\TextColumn::make('expire_at')
//                    ->dateTime()
//                    ->sortable(),
//                Tables\Columns\TextColumn::make('customer_gstin')
//                    ->searchable(),
                Tables\Columns\IconColumn::make('shipping_is_billing')
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







}
