<?php

namespace App\Filament\Resources\Order\OrderResource\Pages;

use App\Filament\Resources\Order\OrderResource;
use App\Helpers\Money\Money;
use App\Models\Order\Order;
use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\Tabs;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }


    public function mount(int|string $record): void
    {
        parent::mount($record);
    }


    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([


                Tabs::make('Label')
                    ->tabs([
                        Tabs\Tab::make('Order')
                            ->columns(2)
                            ->schema($this->orderInfoSchema()),
                        Tabs\Tab::make('Product')
                            ->columns(2)
                            ->schema($this->orderProductInfoSchema()),
                        Tabs\Tab::make('Payment')
                            ->columns(2)
                            ->schema($this->orderPaymentInfoSchema()),
                        Tabs\Tab::make('Shipping')
                            ->columns(2)
                            ->schema($this->orderShippingInfoSchema()),

                    ])->columnSpanFull()->contained(false),


















            ]);
    }

    private function orderInfoSchema()
    {
        return [
            TextEntry::make('uuid')
                ->hiddenLabel()
                ->size(TextEntry\TextEntrySize::Large)
                ->weight(FontWeight::Bold)
                ->color('primary'),


            TextEntry::make('tracking_id')
                ->hiddenLabel()
                ->size(TextEntry\TextEntrySize::Medium)
                ->weight(FontWeight::Medium)
                ->default('--not found any tracking data--')
                ->copyable()
                ->copyMessage('copied!'),

            TextEntry::make('status')
                ->formatStateUsing(function ($state){
                    return Order::StatusOptions[$state];
                })
                ->color(fn (string $state): string => match ($state) {
                    Order::PENDING,Order::REVIEW => 'gray',
                    Order::PROCESSING,Order::INTRANSIT,Order::READYTOSHIP => 'warning',
                    Order::ACCEPTED,Order::CONFIRM,Order::COMPLETED => 'success',
                    Order::REFUNED,Order::PAYMENT_FAILED,Order::CANCELLED => 'danger',
                })

                ->badge(),


            TextEntry::make('quantity')
                ->numeric()
                ->size(TextEntry\TextEntrySize::Large)
                ->weight(FontWeight::Bold),


            Split::make([
                TextEntry::make('amount')
                    ->formatStateUsing(function ($state){
                        return ($state instanceof Money) ? $state->formatted() : $state;
                    }),
                TextEntry::make('subtotal')
                    ->formatStateUsing(function ($state){
                        return ($state instanceof Money) ? $state->formatted() : $state;
                    }),

                TextEntry::make('discount')
                    ->formatStateUsing(function ($state){
                        return ($state instanceof Money) ? $state->formatted() : $state;
                    }),

                TextEntry::make('tax')
                    ->formatStateUsing(function ($state){
                        return ($state instanceof Money) ? $state->formatted() : $state;
                    }),

                TextEntry::make('total')
                    ->formatStateUsing(function ($state){
                        return ($state instanceof Money) ? $state->formatted() : $state;
                    }),
            ]),


            TextEntry::make('voucher'),
            IconEntry::make('payment_success')->default(false)->boolean(),
            IconEntry::make('shipping_is_billing')->default(false)->boolean(),
            TextEntry::make('customer_gstin'),
            TextEntry::make('customer.email'),


        ];
    }

    public function orderProductInfoSchema(): array
    {
        return [

            RepeatableEntry::make('orderProducts')

                ->schema([
                    TextEntry::make('product.name'),
                    TextEntry::make('quantity'),
                ])
                ->columns(2)


        ];
    }

    public function orderPaymentInfoSchema(): array
    {
        return [

            TextEntry::make('payment.receipt'),

            Split::make([
                TextEntry::make('payment.provider_gen_id')
                   ->label('Order ID'),

                TextEntry::make('payment.provider_ref_id')
                   ->label('Payment ID'),

                TextEntry::make('payment.provider_gen_sign')
                  ->label('Payment Sign'),


            ]),

            TextEntry::make('payment.voucher')

                ->label('Voucher'),

            TextEntry::make('payment.quantity')
                ->numeric()
                ->label('Quantity'),


//            Split::make([
//                TextEntry::make('subtotal')
//                    ->formatStateUsing(function ($state){
//                        return ($state instanceof Money) ? $state->formatted() : $state;
//                    }),
//
//                TextEntry::make('discount')
//                    ->formatStateUsing(function ($state){
//                        return ($state instanceof Money) ? $state->formatted() : $state;
//                    }),
//
//                TextEntry::make('tax')
//                    ->formatStateUsing(function ($state){
//                        return ($state instanceof Money) ? $state->formatted() : $state;
//                    }),
//
//                TextEntry::make('total')
//                    ->formatStateUsing(function ($state){
//                        return ($state instanceof Money) ? $state->formatted() : $state;
//                    }),
//            ]),



        ];

    }

    public function orderShippingInfoSchema(): array
    {
        return [

            RepeatableEntry::make('shipments')
                ->hiddenLabel()
                ->columnSpanFull()
                //->contained(false)
                ->columns(2)
                ->schema([
                    TextEntry::make('invoice_uid'),
                    TextEntry::make('total_quantity'),
                    TextEntry::make('tracking_id'),
                    TextEntry::make('last_update'),
                    TextEntry::make('status'),
                    IconEntry::make('cod')->default(false)->boolean(),
                    TextEntry::make('pickupAddress.address_1'),
                    TextEntry::make('deliveryAddress.address_1'),
                    TextEntry::make('shippingProvider.name'),
                ])


        ];
    }


}
