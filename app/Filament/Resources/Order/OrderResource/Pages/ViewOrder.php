<?php

namespace App\Filament\Resources\Order\OrderResource\Pages;

use App\Filament\Resources\Order\OrderResource;
use App\Helpers\Money\Money;
use App\Models\Order\Order;
use App\Services\OrderService\OrderShippedService;
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
        return [
            Actions\EditAction::make(),
//            Action::make('calculate_charge')
//                ->fillForm(function (?Model $record){
//                    $record->load('shipments');
//                    $data = $record->toArray();
//                    $data['amount'] = ($data['amount'] instanceof Money) ? $data['amount']->getAmount() : $data['amount'];
//                    $data['subtotal'] = ($data['subtotal'] instanceof Money) ? $data['subtotal']->getAmount() : $data['subtotal'];
//                    $data['discount'] = ($data['discount'] instanceof Money) ? $data['discount']->getAmount() : $data['discount'];
//                    $data['tax'] = ($data['tax'] instanceof Money) ? $data['tax']->getAmount() : $data['tax'];
//                    $data['total'] = ($data['total'] instanceof Money) ? $data['total']->getAmount() : $data['total'];
//                   return $data;
//                })
//                ->form([
//                    TextInput::make('shipments.weight')
//
//
//                ])
//                ->action(function (array $data){
//                    dd($data);
//                })
//                ->modalHeading('Shipping Charge Calculator'),

//
            Actions\Action::make('makeShipping')
                ->color('success')
                ->action(function (ShippingService $shippingService){
                    $orderShipService = new OrderShippedService($this->record,$shippingService);
                    if ($orderShipService->send())
                    {
                        // Successfully All Shipment Of This Order Are Placed Booking On Provider

                    }else{
                        Notification::make()
                            ->title('Opps! Order not shipped..')
                            ->body($orderShipService->getError())
                            ->send();
                    }
                }),
           Actions\Action::make('returnOrder')->color('danger')
//               ->action(function (ShippingService $shippingService){
//                   $orderReturnService = new OrderReturnService($this->record,$shippingService);
//                   if ($orderReturnService->return())
//                   {
//                       // Successfully Return
//
//                   }else{
//                       Notification::make()
//                           ->title('Return Not Made!')
//                           ->body($orderReturnService->getError())
//                           ->send();
//                   }
//               }),

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


}
