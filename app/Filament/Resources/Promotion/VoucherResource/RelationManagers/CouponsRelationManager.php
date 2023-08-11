<?php

namespace App\Filament\Resources\Promotion\VoucherResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CouponsRelationManager extends RelationManager
{
    protected static string $relationship = 'coupons';

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->hint(__('Max: 250'))
                    ->columnSpanFull()
                    ->maxLength(250),


                Fieldset::make('Voucher Timeline & Usage')
                    ->schema([
                        DateTimePicker::make('starts_from')->required()->placeholder('Set Start Date And Time'),
                        DateTimePicker::make('ends_till')->required()->placeholder('Set End Date And Time'),
                        TextInput::make('usage_per_customer')
                            ->label('Usage Per Customer')
                            ->required(),
                        TextInput::make('coupon_usage_limit')
                            ->label('Coupon Usage Limit')
                            ->required(),
                    ])->columns(2),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('coupon_usage_limit')->label('Usage Limit'),
                Tables\Columns\TextColumn::make('usage_per_customer')->label('Per Customer'),
                Tables\Columns\TextColumn::make('times_used')->label('Total Used'),
                Tables\Columns\TextColumn::make('starts_from')->since()->label(__('Start From')),
                Tables\Columns\TextColumn::make('ends_till')->since()->label(__('Ends On')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->disableCreateAnother(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
