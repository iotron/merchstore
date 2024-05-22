<?php

namespace App\Filament\Resources\Order\OrderResource\Pages;

use App\Filament\Resources\Order\OrderResource;
use App\Models\Order\Order;
use App\Models\Order\OrderShipment;
use App\Models\Payment\Refund;
use App\Models\Shipping\ShippingProvider;
use App\Services\Iotron\MoneyService\Money;
use App\Services\OrderService\Return\OrderRefundPayService;
use App\Services\OrderService\Shipping\OrderShipmentShippingService;
use App\Services\PaymentService\PaymentService;
use App\Services\ShippingService\ShippingService;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public array $chargeInfo = [];

    protected function getHeaderActions(): array
    {
        return array_merge($this->getCustomHeaderActions(), [

            Actions\EditAction::make(),

        ]);
    }

    protected function getCustomHeaderActions(): array
    {
        return [

            //
            //            Actions\Action::make('refund')
            //                ->color('danger')
            ////                ->visible(!$this->record->is_cod)
            //                ->action(function (PaymentService $paymentService){
            //                    $refundService = new OrderRefundService($this->record,$paymentService);
            //                    if ($refundService->refund())
            //                    {
            //                        // Successfully Refund
            //                        Notification::make()
            //                            ->success()
            //                            ->title('Refund Made Successfully')
            //                            ->body('')
            //                            ->send();
            //
            //
            //                    }else{
            //                        Notification::make()
            //                            ->danger()
            //                            ->title('Refund Abort!')
            //                            ->body($refundService->getError())
            //                            ->send();
            //                    }
            //                }),
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
                        Tabs\Tab::make('Products')
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
                ->formatStateUsing(function ($state) {
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
                    ->formatStateUsing(function ($state) {
                        return ($state instanceof Money) ? $state->formatted() : $state;
                    }),
                TextEntry::make('subtotal')
                    ->formatStateUsing(function ($state) {
                        return ($state instanceof Money) ? $state->formatted() : $state;
                    }),

                TextEntry::make('discount')
                    ->formatStateUsing(function ($state) {
                        return ($state instanceof Money) ? $state->formatted() : $state;
                    }),

                TextEntry::make('tax')
                    ->formatStateUsing(function ($state) {
                        return ($state instanceof Money) ? $state->formatted() : $state;
                    }),

                TextEntry::make('total')
                    ->formatStateUsing(function ($state) {
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

    public function orderPaymentInfoSchema(): array
    {
        return [
            TextEntry::make('payment.provider.name')->label('Payment Provider'),
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

            RepeatableEntry::make('refunds')
                ->visible($this->record->refunds()->count())
                ->columnSpanFull()
                //->contained(false)
                ->columns(2)
                ->schema([
                    TextEntry::make('refund_id'),
                    TextEntry::make('amount')
                        ->hint(function ($state) {
                            return 'Format : '.$state  instanceof Money ? $state->formatted() : Money::format($state);
                        })
                        ->formatStateUsing(function ($state) {
                            return $state  instanceof Money ? $state->formatted() : $state;
                        }),
                    TextEntry::make('receipt'),
                    TextEntry::make('payment_id'),
                    TextEntry::make('status')->badge(),
                    IconEntry::make('verified')
                        ->boolean()
                        ->hintAction(\Filament\Infolists\Components\Actions\Action::make('refund_amount_action')
                            ->disabled(function (Model $record) {
                                return $record->status != Refund::PENDING;
                            })
                            ->label(function (Model $record) {
                                return $record->status == Refund::PENDING ? 'Refund Amount' : 'Refuned';
                            })
                            ->action(function (array $data, Model $record, PaymentService $paymentService) {
                                $record->loadMissing('payment');
                                $paymentProviderModel = $paymentService->getAllProvidersModel()->firstWhere('id', '=', $record->payment->payment_provider_id);
                                $paymentProviderService = $paymentService->provider($paymentProviderModel->code)->getProvider();

                                $refundPayService = new OrderRefundPayService($paymentProviderService, $record);

                                if ($refundPayService->refund()) {
                                    Notification::make()
                                        ->success()
                                        ->title('Refund Complete')
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->danger()
                                        ->title('Refund Abort')
                                        ->body($refundPayService->getError())
                                        ->send();
                                }

                            })
                        ),

                ]),

        ];

    }

    public function orderShippingInfoSchema(): array
    {
        return [

            RepeatableEntry::make('shipments')
                ->hiddenLabel()
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Split::make([

                        ViewEntry::make('pickup')
                            ->view('filament-custom.forms.address-placeholder')
                            ->getStateUsing(function (Model $record) {
                                return $record->pickupAddress()->first()->toArray();
                            })
                            ->viewData([
                                'label' => 'Pickup Address',
                                'textAlign' => 'right',
                            ]),

                        ViewEntry::make('delivery')
                            ->view('filament-custom.forms.address-placeholder')
                            ->getStateUsing(function (Model $record) {
                                return $record->deliveryAddress()->first()->toArray();
                            })
                            ->viewData([
                                'label' => 'Delivery Address',
                            ]),
                    ]),
                    TextEntry::make('total_quantity')->inlineLabel(),
                    TextEntry::make('invoice_uid')->inlineLabel(),
                    TextEntry::make('tracking_id')->inlineLabel()->default('--not found--'),
                    TextEntry::make('status')->badge()->inlineLabel(),

                    TextEntry::make('shippingProvider.name')
                        ->inlineLabel(),
                    IconEntry::make('cod')
                        ->inlineLabel()
                        ->default(false)->boolean(),
                    TextEntry::make('last_update')->inlineLabel(),

                    TextEntry::make('weight')->default('0.00'),
                    TextEntry::make('length')->default('0.00'),
                    TextEntry::make('breadth')->default('0.00'),
                    TextEntry::make('height')->default('0.00'),

                ]),

        ];
    }

    // Basic
    public function orderProductInfoSchema(): array
    {
        return [

            RepeatableEntry::make('orderProducts')
                ->grid(2)
                ->columns(2)
                ->schema([

                    TextEntry::make('product.name')
                        ->hiddenLabel()
                        ->columnSpanFull()
                        ->icon('heroicon-m-briefcase')
                        ->size(TextEntry\TextEntrySize::Large),

                    TextEntry::make('product.sku')
                        ->label('SKU')
                        ->size(TextEntry\TextEntrySize::Large),

                    IconEntry::make('product.is_returnable')
                        ->default(false)
                        ->label('Returnable')
                        ->boolean(),

                    TextEntry::make('order.payment.provider.name')->label('Pay With'),

                    TextEntry::make('quantity')->hiddenLabel()
                        ->formatStateUsing(function ($state) {
                            return $state.' (Qty)';
                        })->badge(),

                    TextEntry::make('amount')
                        ->formatStateUsing(function ($state) {
                            return ($state instanceof Money) ? $state->formatted() : $state;
                        }),

                    TextEntry::make('discount')
                        ->formatStateUsing(function ($state) {
                            return ($state instanceof Money) ? $state->formatted() : $state;
                        }),

                    TextEntry::make('tax')
                        ->formatStateUsing(function ($state) {
                            return ($state instanceof Money) ? $state->formatted() : $state;
                        }),

                    TextEntry::make('total')
                        ->formatStateUsing(function ($state) {
                            return ($state instanceof Money) ? $state->formatted() : $state;
                        }),
                    //                    IconEntry::make('has_tax')->default(false)->boolean(),

                    RepeatableEntry::make('shipment')
                        ->columns(3)
                        ->columnSpanFull()
                        ->schema([
                            TextEntry::make('status')
                                ->badge()
                                ->formatStateUsing(function ($state) {
                                    return OrderShipment::StatusOptions[$state];
                                })
                                ->color(fn (string $state): string => match ($state) {
                                    OrderShipment::PROCESSING => 'gray',
                                    OrderShipment::REVIEW,OrderShipment::READYTOSHIP,OrderShipment::PACKING => 'warning',
                                    OrderShipment::DELIVERED,OrderShipment::INTRANSIT,OrderShipment::RETURNED => 'success',
                                    OrderShipment::CANCELLED,OrderShipment::RETURNING => 'danger',
                                }),
                            TextEntry::make('total_quantity')->label('Quantity'),
                            IconEntry::make('cod')->default(false)->boolean(),
                            TextEntry::make('provider_payment_method')
                                ->label('Payment Mode')
                                ->tooltip('Shipping Provider Payment Mode')
                                ->default('--not found--'),
                            TextEntry::make('provider_order_id')
                                ->default('--not found--'),
                            TextEntry::make('tracking_id')->default('--not found--'),
                            TextEntry::make('return_order_id')
                                ->visible(function ($state) {
                                    return ! is_null($state);
                                })
                                ->default('--not found--'),

                            TextEntry::make('return_shipment_id')
                                ->visible(function ($state) {
                                    return ! is_null($state);
                                })
                                ->default('--not found--'),

                            TextEntry::make('pickupAddress.address_1')
                                ->columnSpanFull()
                                ->hint(function (Model $record) {
                                    if ($record->status == OrderShipment::PROCESSING) {
                                        if (is_null($record->weight) && is_null($record->length) && is_null($record->breadth) && is_null($record->height)) {
                                            return 'Not Ready For Shipment';
                                        } else {
                                            return 'Ready For Shipment ';
                                        }
                                    } else {
                                        return 'check shipment section for progress';
                                    }

                                })
                                ->hintAction(
                                    \Filament\Infolists\Components\Actions\Action::make('make_shipment')
                                        ->requiresConfirmation()
                                        ->form([
                                            Select::make('shipping_provider')
                                                ->options(ShippingProvider::where('status', '=', true)->get()->pluck('name', 'code'))
                                                ->helperText('Choose Shipping Method')
                                                ->required(),
                                        ])
                                        ->visible(function (Model $record) {
                                            return ! is_null($record->weight) && ! is_null($record->length) && ! is_null($record->breadth) && ! is_null($record->height) && ($record->status == OrderShipment::PROCESSING);
                                        })
                                        ->action(function (array $data, ShippingService $shippingService, Model $record) {
                                            $shippingProvider = $shippingService->provider($data['shipping_provider'])->getProvider();
                                            $shipmentShipService = new OrderShipmentShippingService($record, $shippingProvider);

                                            if ($shipmentShipService->shipped()) {
                                                // Send A Notification
                                                Notification::make()
                                                    ->success()
                                                    ->title('OrderShipment ID:'.$record->id.' Ready For Shipped!')
                                                    ->body('Shipping Request Placed By '.ucfirst($shippingProvider->getProviderName()).' Shipping Service')
                                                    ->send();

                                            } else {
                                                Notification::make()
                                                    ->title('Abort!')
                                                    ->body($shipmentShipService->getError())
                                                    ->danger()
                                                    ->send();
                                            }

                                        })
                                ),
                        ]),

                ]),

        ];
    }
}
