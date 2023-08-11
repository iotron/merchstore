<?php

namespace App\Filament\Resources\Customer\CustomerResource\Pages;

use App\Filament\Resources\Customer\CartCustomerResource;
use App\Filament\Resources\Customer\CustomerResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }



    protected function getTableActions(): array
    {
        return array_merge([

            Action::make('customer_cart_list')

                ->label('Cart')
                ->tooltip('Cart Details')
                ->icon('heroicon-o-gift')
                ->url(fn($record): string => static::getResource()::hasPage('cart') ? static::getResource()::getUrl('cart', ['record' => $record]) : null)

                ->openUrlInNewTab(false),

        ],parent::getTableActions());
    }


    public  function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->toggleable()->searchable(),
                TextColumn::make('email')->toggleable(),
                TextColumn::make('contact')->formatStateUsing(function ($state){
                    return is_null($state) ? 'undefined' : $state;
                })->toggleable(),


                IconColumn::make('contact_verified_at')
                    ->label(__('Contact Verified'))
                    ->boolean()
                    ->default(false)
                    ->sortable()->toggleable()->toggledHiddenByDefault(),
                IconColumn::make('email_verified_at')
                    ->label(__('Email Verified'))
                    ->boolean()
                    ->default(false)
                    ->sortable()->toggleable()->toggledHiddenByDefault(),



                TextColumn::make('created_at')->label(__('Created On'))
                    ->dateTime()->since()->toggleable()->toggledHiddenByDefault(),
                TextColumn::make('updated_at')->label(__('Updated On'))
                    ->dateTime()->since()->toggleable(),
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
