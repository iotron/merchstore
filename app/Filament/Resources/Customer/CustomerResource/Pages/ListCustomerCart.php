<?php

namespace App\Filament\Resources\Customer\CustomerResource\Pages;

use App\Filament\Resources\Customer\CustomerResource;
use App\Models\Customer\Customer;
use App\Models\Product\Product;
use App\Services\Iotron\MoneyService\Money;
use Filament\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ListCustomerCart extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected static ?string $title = 'Customer Cart';

    public $customerId;

    public $customer;

    public $scannedData;

    public function mount($record = ''): void
    {
        $this->customerId = $record;
        $this->customer = Customer::find($record);
        $this->record = $this->customer;
    }

    // TABLE AREA
    // _______________________________________________________________________________
    public function getTableQuery(): Builder
    {
        return $this->customer->cart()->getQuery();
    }

    public function table(Table $table): Table
    {

        return $table
            ->columns([

                TextColumn::make('sku')->label(__('SKU'))->formatStateUsing(function ($record) {
                    return $record->sku;
                }),

                TextColumn::make('price')->label(__('Price'))->formatStateUsing(function ($record) {
                    return $record->price->formatted();
                }),

                TextColumn::make('quantity'),
                TextColumn::make('discount')->formatStateUsing(function ($record) {
                    $discount = new Money($record->discount);

                    return $discount->formatted();
                }),
                TextColumn::make('total')->formatStateUsing(function ($record) {

                    $total = $record->price->multiplyOnce($record->quantity);

                    return $total->formatted();

                }),

            ])
            ->actions([

                \Filament\Tables\Actions\Action::make('remove')
                    ->url(function (Model $record) {

                        return route('remove-cart', [
                            'customer' => $record->customer_id,
                            'product' => $record->product_id,
                        ]);
                    })
                //->visible(fn (Product $record): bool => auth()->user()->can('remove', $record))
                ,

            ])
            ->filters([]);

    }

    //    public function getTableRecordTitle($record): string
    //    {
    //        if ($record instanceof \Illuminate\Database\Eloquent\Model) {
    //            // Modify this code to return the appropriate title for the record
    //            return $record->name;
    //        }
    //
    //        return '';
    //    }

    // PAGE ACTIONS

    protected function getHeaderActions(): array
    {
        return [

            Action::make('scan_sku')
                ->label(__('Scan SKU'))
                ->action('scanQr')
                ->color('warning')
                ->modalContent(view('filament.custom.qr-scanner')),

            Action::make('addProduct')
                ->label(__('Add To Cart'))
                ->color('primary')
                ->action(function (array $data): void {
                    if ($data['stock'] >= $data['quantity']) {
                        $this->customer->cart()->attach($data['product_id'], ['quantity' => $data['quantity'], 'discount' => $data['discount']]);
                    }

                })
                ->form($this->customPOSForm()),

            Action::make('placeOrder')
                ->label(__('Place Order'))
                ->color('success')
                ->action('placeOrder')
                ->visible(function () {
                    return $this->customer->cart->count();
                })->requiresConfirmation(),

        ];
    }

    // MODAL FORM
    public function customPOSForm()
    {
        return [

            Select::make('product_id')
                ->options(Product::where('status', '=', Product::PUBLISHED)->get()->pluck('sku', 'id'))
                ->label(__('Select Product'))
                ->searchable()
                ->placeholder(__('select or search product by sku'))
                ->lazy()
                ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                    $selectedProduct = Product::firstWhere('id', $state);
                    $set('stock', $selectedProduct->availableStocks()->sum('in_stock_quantity'));
                    $set('sku', $selectedProduct->sku);
                    $set('name', $selectedProduct->name);
                    $set('formatted_price', $selectedProduct->price->formatted());
                }),

            Fieldset::make('product_details')
                ->label(__('Bill Details'))
                ->schema([
                    TextInput::make('stock')->inlineLabel()->disabled(),
                    TextInput::make('name')->inlineLabel()->disabled(),
                    TextInput::make('sku')->inlineLabel()->disabled(),
                    TextInput::make('formatted_price')->label(__('Price'))->inlineLabel()->disabled(),

                    TextInput::make('quantity')
                        ->numeric()
                        ->inlineLabel()
                        ->minValue(1)
                        ->maxValue(999999999)
                        ->lazy()
                        ->afterStateUpdated(function (\Filament\Forms\Get $get, \Filament\Forms\Set $set, $state) {
                            $stockCount = $get('stock');
                            if ($state <= $stockCount) {
                                $product = Product::firstWhere('id', $get('product_id'));
                                $total = $product->price->multiplyOnce($state);
                                $set('total', $total->getAmount());
                                $set('formatted_total', $total->formatted());
                            } else {
                                $set('formatted_total', 'Error');
                            }
                        })
                        ->helperText(function (\Filament\Forms\Get $get) {
                            return empty($get('stock')) ? 'no stock available' : 'available stock :'.$get('stock');
                        })
                        ->disabled(function (\Filament\Forms\Get $get) {
                            return empty($get('stock'));
                        })
                        ->required(),

                    TextInput::make('discount')
                        ->label(__('Discount'))
                        ->helperText(__('Use Promo Code'))
                        ->numeric()
                        ->inlineLabel()
                        ->minValue(1)
                        ->maxValue(999999999)
                        ->lazy()
                        ->afterStateUpdated(function (\Filament\Forms\Get $get, \Filament\Forms\Set $set, $state) {

                            if (! empty($state)) {
                                $stockCount = $get('stock');
                                $quantityAsk = $get('quantity');
                                if ($quantityAsk <= $stockCount) {
                                    $discount = new Money($state);
                                    $product = Product::firstWhere('id', $get('product_id'));
                                    // Flat Discount On Cart Total
                                    $total = $product->price->multiplyOnce($quantityAsk)->subOnce($discount);
                                    $set('total', $total->getAmount());
                                    $set('formatted_total', $total->formatted());
                                } else {
                                    $set('formatted_total', 'Error');
                                }
                            } else {
                                $stockCount = $get('stock');
                                $quantityAsk = $get('quantity');
                                if ($quantityAsk <= $stockCount) {
                                    $product = Product::firstWhere('id', $get('product_id'));
                                    $total = $product->price->multiplyOnce($quantityAsk);

                                    $set('total', $total->getAmount());
                                    $set('formatted_total', $total->formatted());
                                }
                            }

                        }),

                    TextInput::make('total')->disabled()->inlineLabel()->hidden(),
                    TextInput::make('formatted_total')->label(__('Total'))->disabled()->inlineLabel(),

                ])->visible(function (\Filament\Forms\Get $get) {
                    return ! empty($get('product_id'));
                })->columns(1),

        ];
    }

    // METHODS

    public function getProductSelectOption()
    {
        return $this->categoryBag->mapWithKeys(
            function ($item) {
                return [$item['id'] => $item['name'].' - '.$item['desc']];
            }
        )->toArray();
    }

    public function scanQr()
    {
        // Access the scanned data from the form submission
        $data = $this->scannedData;

        dd($data);

        // Process the scanned data and perform necessary actions
        // ...

        // Redirect or return a response if needed
        // ...
    }

    public function placeOrder()
    {

        $cart = $this->customer->cart;

        if ($cart->count()) {
            $cartData = $this->calculateCart($cart);

        }

    }

    protected function calculateCart(array|Collection $cartDetail)
    {

        $cartMeta = [];
        $subTotal = new Money(0);
        $totalDiscount = new Money(0);
        $totalQuantity = 0;

        foreach ($cartDetail as $item) {

            $totalQuantity = $totalQuantity + $item->pivot->quantity;
            $subTotal->add($item->price->multiplyOnce($item->pivot->quantity));
            $totalDiscount->add($item->pivot->discount);

            $cartMeta[$item->id] = [
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'quantity' => $item->pivot->quantity,
                'price' => $item->price->getAmount(),
                'price_formatted' => $item->price->formatted(),
                'discount' => $item->pivot->discount,

                'subTotal' => $item->price->multiplyOnce($item->pivot->quantity),
                'subTotal_formatted' => $item->price->multiplyOnce($item->pivot->quantity)->formatted(),
            ];
        }

        dd([
            'quantity' => $totalQuantity,
            'subTotal' => $subTotal->formatted(),
            'discount' => $totalDiscount->formatted(),
            'customer' => $this->customer,
            'meta' => $cartMeta,
        ]);

    }
}
