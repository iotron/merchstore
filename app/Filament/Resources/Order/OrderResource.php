<?php

namespace App\Filament\Resources\Order;

use App\Filament\Resources\Order\OrderResource\Pages;
use App\Filament\Resources\Order\OrderResource\RelationManagers;
use App\Helpers\Money\Money;
use App\Models\Order\Order;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Order Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->required(),
                Forms\Components\TextInput::make('payment_provider_id')
                    ->numeric(),
                Forms\Components\Select::make('billing_address_id')
                    ->relationship('billingAddress', 'id'),
                Forms\Components\Select::make('shipping_address_id')
                    ->relationship('shippingAddress', 'id'),
                Forms\Components\TextInput::make('uuid')
                    ->label('UUID')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('discount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('tax')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('voucher')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tracking_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('pending'),
                Forms\Components\Toggle::make('payment_success')
                    ->required(),
                Forms\Components\DateTimePicker::make('expire_at')
                    ->required(),
                Forms\Components\TextInput::make('customer_gstin')
                    ->maxLength(255),
                Forms\Components\Toggle::make('shipping_is_billing')
                    ->required(),
            ]);
    }



    public static function getRelations(): array
    {
        return [
//            RelationManagers\OrderProductsRelationManager::class,
//            RelationManagers\ShipmentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
