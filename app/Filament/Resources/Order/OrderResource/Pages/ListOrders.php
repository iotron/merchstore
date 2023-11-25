<?php

namespace App\Filament\Resources\Order\OrderResource\Pages;

use App\Filament\Resources\Order\OrderResource;
use App\Helpers\Money\Money;
use App\Models\Order\Order;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status','=', Order::PENDING)),
            'confirm' => Tab::make('Confirm')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status','=', Order::CONFIRM)),
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
                    ->label('Provider')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(function ($state){
                        return Order::StatusOptions[$state];
                    })
                    ->color(fn (string $state): string => match ($state) {
                        Order::PENDING,Order::REVIEW => 'gray',
                        Order::PROCESSING,Order::INTRANSIT,Order::READYTOSHIP => 'warning',
                        Order::ACCEPTED,Order::CONFIRM,Order::COMPLETED => 'success',
                        Order::REFUNED,Order::PAYMENT_FAILED,Order::CANCELLED => 'danger',
                    })
                    ->searchable(),
                Tables\Columns\IconColumn::make('payment_success')
                    ->label('Payment')
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
            ->defaultSort('created_at','desc')
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
