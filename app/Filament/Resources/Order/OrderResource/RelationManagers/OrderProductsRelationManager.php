<?php

namespace App\Filament\Resources\Order\OrderResource\RelationManagers;

use App\Services\Iotron\MoneyService\Money;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OrderProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderProducts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('product_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_id')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Name'),
                Tables\Columns\TextColumn::make('product.sku')->label('SKU'),
                Tables\Columns\TextColumn::make('quantity')->label('Quantity'),
                Tables\Columns\TextColumn::make('amount')->label('Amount')
                    ->formatStateUsing(function ($state) {
                        return ($state instanceof Money) ? $state->formatted() : $state;
                    }),
                Tables\Columns\TextColumn::make('discount')->label('Discount')
                    ->formatStateUsing(function ($state) {
                        return ($state instanceof Money) ? $state->formatted() : $state;
                    }),
                Tables\Columns\TextColumn::make('tax')->label('Tax')
                    ->formatStateUsing(function ($state) {
                        return ($state instanceof Money) ? $state->formatted() : $state;
                    }),
                Tables\Columns\TextColumn::make('total')->label('Total')
                    ->formatStateUsing(function ($state) {
                        return ($state instanceof Money) ? $state->formatted() : $state;
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->fillForm(function (Model $record) {
                    $data = $record->toArray();

                    return $this->normalizeFillableData($data);
                }),
                Tables\Actions\EditAction::make()
                    ->fillForm(function (Model $record) {
                        $data = $record->toArray();

                        return $this->normalizeFillableData($data);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function normalizeFillableData(array $data): array
    {
        $data['amount'] = ($data['amount'] instanceof Money) ? $data['amount']->getAmount() : $data['amount'];
        $data['discount'] = ($data['discount'] instanceof Money) ? $data['discount']->getAmount() : $data['discount'];
        $data['tax'] = ($data['tax'] instanceof Money) ? $data['tax']->getAmount() : $data['tax'];
        $data['total'] = ($data['total'] instanceof Money) ? $data['total']->getAmount() : $data['total'];
        if (isset($data['product'])) {
            $data['product']['base_price'] = ($data['product']['base_price'] instanceof Money) ? $data['product']['base_price']->getAmount() : $data['product']['base_price'];
            $data['product']['tax_amount'] = ($data['product']['tax_amount'] instanceof Money) ? $data['product']['tax_amount']->getAmount() : $data['product']['tax_amount'];
            $data['product']['price'] = ($data['product']['price'] instanceof Money) ? $data['product']['price']->getAmount() : $data['product']['price'];
        }

        return $data;
    }
}
