<?php

namespace App\Filament\Resources\Payment\PaymentProviderResource\Pages;

use App\Filament\Resources\Payment\PaymentProviderResource;
use App\Models\Payment\PaymentProvider;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Str;

class ListPaymentProviders extends ListRecords
{
    protected static string $resource = PaymentProviderResource::class;

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


                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('name')
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Large)
                        ->weight(FontWeight::Bold)
                        ->searchable(),
                    Tables\Columns\TextColumn::make('code')
                        ->alignRight()
                        ->badge()
                        ->searchable(),

                ])->grow(),

                Tables\Columns\Layout\Split::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('service_provider')
                            ->tooltip('Service Provider')
                            ->formatStateUsing(function ($state){
                                return Str::remove('App\Services\PaymentService',$state);
                            })
                            ->grow()
                            ->weight(FontWeight::Thin)
                            ->searchable(),
                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('payments_count')->counts('payments')
                                ->description('Payments')
                                ->tooltip('Total Payments Init')
                                ->weight(FontWeight::Bold),
                            Tables\Columns\TextColumn::make('orders_count')->counts('orders')
                                ->description('Orders')
                                ->tooltip('Total Orders Init')
                                ->weight(FontWeight::Bold),

                        ]),
                    ]),

                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\IconColumn::make('is_primary')
                            ->boolean()
                            ->tooltip('Is Primary Provider?')
                            ->alignRight(),
                        Tables\Columns\IconColumn::make('has_api')
                            ->tooltip('Is Provider has Api')
                            ->boolean()->alignRight(),
                        Tables\Columns\IconColumn::make('status')
                            ->tooltip('Provider Status')
                            ->boolean()->alignRight(),
                    ])
                ]),




                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('created_at')
                        ->dateTime()
                        ->sortable()
                        ->description('Created On')
                        ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('updated_at')
                        ->dateTime()
                        ->sortable()
                        ->alignRight()
                        ->description('Update On')
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),




            ])
            ->contentGrid([
                'md' => 2
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
