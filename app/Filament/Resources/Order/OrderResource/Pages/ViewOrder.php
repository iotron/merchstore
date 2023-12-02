<?php

namespace App\Filament\Resources\Order\OrderResource\Pages;

use App\Filament\Resources\Order\OrderResource;
use App\Helpers\Money\Money;
use App\Models\Order\Order;
use App\Models\Order\OrderShipment;
use App\Models\Shipping\ShippingProvider;
use App\Services\OrderService\OrderRefundService;
use App\Services\OrderService\OrderReturnService;
use App\Services\OrderService\OrderShippedService;
use App\Services\OrderService\Return\OrderProductReturnService;
use App\Services\OrderService\Shipping\OrderShipmentShippingService;
use App\Services\PaymentService\PaymentService;
use App\Services\ShippingService\ShippingService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\Tabs;
use Illuminate\Database\Eloquent\Model;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;
    public array $chargeInfo = [];

    protected function getHeaderActions(): array
    {
        return array_merge($this->getCustomHeaderActions(),[

            Actions\EditAction::make(),

        ]);
    }


    protected function getCustomHeaderActions():array
    {
        return [
            // Whole Order OneTime Shipment
            Actions\Action::make('makeShipping')
                ->label('Place Custom Shipment')
                ->color('success')
                ->action(function (ShippingService $shippingService){

                    $orderShipService = new OrderShippedService($this->record,$shippingService);
                    if ($orderShipService->send())
                    {
                        // Send A Notification
                        Notification::make()
                            ->success()
                            ->title('Order  Ready For Shipped!')
                            ->send();
                        // Successfully All Shipment Of This Order Are Placed Booking On Provider

                    }else{
                        Notification::make()
                            ->title('Opps! Order not shipped..')
                            ->body($orderShipService->getError())
                            ->send();
                    }
                }),


            // Return Order Action
            Actions\Action::make('returnOrder')->color('warning')
                ->action(function (ShippingService $shippingService){
                    $orderReturnService = new OrderReturnService($this->record,$shippingService);
                    if ($orderReturnService->return())
                    {
                        // Successfully Return

                    }else{
                        Notification::make()
                            ->title('Return Not Made!')
                            ->body($orderReturnService->getError())
                            ->send();
                    }
                }),


            // Refund Order Payment

            Actions\Action::make('refund')
                ->color('danger')
//                ->visible(!$this->record->is_cod)
                ->action(function (PaymentService $paymentService){
                    $refundService = new OrderRefundService($this->record,$paymentService);
                    if ($refundService->refund())
                    {
                        // Successfully Refund
                        Notification::make()
                            ->success()
                            ->title('Refund Made Successfully')
                            ->body('')
                            ->send();


                    }else{
                        Notification::make()
                            ->danger()
                            ->title('Refund Abort!')
                            ->body($refundService->getError())
                            ->send();
                    }
                }),
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



//
//            RepeatableEntry::make('refunds')
//                ->visible($this->record->refunds()->count())
//                ->columnSpanFull()
//                ->contained(false)
//                ->columns(2)
//                ->schema([
//                    TextEntry::make('refund.refund_id'),
//                    TextEntry::make('refund.amount')
//                        ->formatStateUsing(function ($state){
//                            return $state  instanceof Money ? $state->formatted() : $state;
//                        }),
//                    TextEntry::make('refund.receipt'),
//                    TextEntry::make('refund.payment_id'),
//                    TextEntry::make('refund.status'),
//                ])



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
                ->schema([
                    Split::make([

                        ViewEntry::make('pickup')
                            ->view('filament-custom.forms.address-placeholder')
                            ->getStateUsing(function (Model $record){
                                return $record->pickupAddress()->first()->toArray();
                            })
                            ->viewData([
                                'label' => 'Pickup Address',
                                'textAlign' => 'right'
                            ]),


                        ViewEntry::make('delivery')
                            ->view('filament-custom.forms.address-placeholder')
                            ->getStateUsing(function (Model $record){
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
                    TextEntry::make('cost')
                        ->hintAction(\Filament\Infolists\Components\Actions\Action::make('calculate')
                            ->label('Calculate Cost')
                            ->fillForm(function (?Model $record){
                                $data = $record->toArray();
                                $data['charge'] = ($data['charge'] instanceof Money) ? $data['charge']->getAmount() : $data['charge'];
                                return $data;
                            })
                            ->form([
                                TextInput::make('weight'),
                                TextInput::make('length'),
                                TextInput::make('breadth'),
                                TextInput::make('height'),
                            ])
                            ->action(function (array $data, ?Model $record,ShippingService $shippingService) use (&$chargeInfo){
                                $record->load('shippingProvider','pickupAddress','deliveryAddress');
//                                        $service = $shippingService->provider($record->shippingProvider->code);
                                $service = $shippingService->provider('shiprocket');
                                $pickUpPostalCode = $record->pickupAddress->postal_code;
                                $deliveryPostalCode = $record->deliveryAddress->postal_code;
//                                        $chargeInfo = $service->courier()->getCharge($pickUpPostalCode,$deliveryPostalCode,$data);
                                $chargeInfo = $service->courier()->getCharge(711401,$deliveryPostalCode,$data,$record->cod);

                                $this->chargeInfo = $chargeInfo;
                            })
                            ->requiresConfirmation()

                        ),


                    $this->getCourierDisplayerInfoSchema()



                ])


        ];
    }

    private function getCourierDisplayerInfoSchema()
    {
        $bag = [];
        if (!empty($this->chargeInfo))
        {
            foreach ($this->chargeInfo['data']['available_courier_companies'] as $company)
            {

             $bag[] =  Section::make('Courier Company Info')
                        ->description('Charges Will Be')
                        ->state($company)
                        ->columns(3)

                        ->schema([
                                TextEntry::make('call_before_delivery')->state($company['call_before_delivery']),
                                TextEntry::make('charge_weight')->state($company['charge_weight']),
                                TextEntry::make('cod')->state($company['cod']),
                                TextEntry::make('cod_charges')->state($company['cod_charges'])->hint(Money::format($company['cod_charges'])),
                                TextEntry::make('cod_multiplier')->state($company['cod_multiplier']),
                                TextEntry::make('courier_name')->state($company['courier_name']),
                                TextEntry::make('coverage_charges')->state($company['coverage_charges'])->hint(Money::format($company['coverage_charges'])),
                                TextEntry::make('estimated_delivery_days')->state($company['estimated_delivery_days']),
                                TextEntry::make('etd')->state($company['etd']),
                                TextEntry::make('etd_hours')->state($company['etd_hours']),
                                TextEntry::make('freight_charge')->state($company['freight_charge'])->hint(Money::format($company['freight_charge'])),
                                IconEntry::make('is_surface')->boolean(),
                                TextEntry::make('message')->state($company['message']),
                                TextEntry::make('min_weight')->state($company['min_weight']),
                                TextEntry::make('rating')->badge()->state($company['rating']),
                                TextEntry::make('rto_charges')->state($company['rto_charges'])->hint(Money::format($company['rto_charges'])),
                                TextEntry::make('seconds_left_for_pickup')->state($company['seconds_left_for_pickup'])
                        ]);

            }

        }

        return Section::make($bag);

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
                        ->boolean()
                        ->hintAction(
                            \Filament\Infolists\Components\Actions\Action::make('return')
                                ->requiresConfirmation()
                                ->visible(function (?Model $record){
                                    return $record->product->is_returnable;
                                })
                                ->action(function (Model $record,ShippingService $shippingService){
                                    $orderProductReturnService = new OrderProductReturnService($shippingService,$record);
                                    if ($orderProductReturnService->return())
                                    {
                                        Notification::make()
                                            ->success()
                                            ->title('Return Placed')
                                            ->body('Return Placed for all shipments')
                                            ->send();

                                    }else{
                                        Notification::make()
                                            ->title('Return Process Abort!')
                                            ->body($orderProductReturnService->getError())
                                            ->danger()
                                            ->send();
                                    }
                                })

                        ),

                    TextEntry::make('order.payment.provider.name')->label('Pay With'),

                    TextEntry::make('quantity')->hiddenLabel()
                        ->formatStateUsing(function ($state){
                            return $state.' (Qty)';
                        })->badge(),

                    TextEntry::make('amount')
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
//                    IconEntry::make('has_tax')->default(false)->boolean(),


                    RepeatableEntry::make('shipment')
                        ->columns(3)
                        ->columnSpanFull()
                        ->schema([
                            TextEntry::make('status')
                                ->badge()
                                ->formatStateUsing(function ($state){
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
                                ->visible(function ($state){
                                    return !is_null($state);
                                })
                                ->default('--not found--'),

                            TextEntry::make('return_shipment_id')
                                ->visible(function ($state){
                                    return !is_null($state);
                                })
                                ->default('--not found--'),

                            TextEntry::make('pickupAddress.address_1')
                                ->columnSpanFull()
                                ->hint(function (Model $record){
                                    if (is_null($record->weight) && is_null($record->length) && is_null($record->breadth) && is_null($record->height))
                                    {
                                        return 'Not Ready For Shipment';
                                    }else{
                                        return 'Ready For Shipment ';
                                    }
                                })
                                ->hintAction(
                                    \Filament\Infolists\Components\Actions\Action::make('make_shipment')
                                        ->requiresConfirmation()
                                        ->form([
                                            Select::make('shipping_provider')
                                                ->options(ShippingProvider::where('status','=',true)->get()->pluck('name','code'))
                                                ->helperText('Choose Shipping Method')
                                                ->required(),
                                        ])
                                        ->visible(function (Model $record){
                                            return !is_null($record->weight) && !is_null($record->length) && !is_null($record->breadth) && !is_null($record->height);
                                        })
                                        ->action(function (array $data,ShippingService $shippingService,Model $record){
                                            $shippingProvider = $shippingService->provider($data['shipping_provider'])->getProvider();
                                            $shipmentShipService = new OrderShipmentShippingService($record,$shippingProvider);

                                            if ($shipmentShipService->shipped())
                                            {
                                                // Send A Notification
                                                Notification::make()
                                                    ->success()
                                                    ->title('OrderShipment ID:'.$record->id.' Ready For Shipped!')
                                                    ->body('Shipping Request Placed By '.ucfirst($shippingProvider->getProviderName()).' Shipping Service')
                                                    ->send();

                                            }else{
                                                Notification::make()
                                                    ->title('Abort!')
                                                    ->body($shipmentShipService->getError())
                                                    ->danger()
                                                    ->send();
                                            }

                                        })
                                )
                        ]),



                ])


        ];
    }



}
