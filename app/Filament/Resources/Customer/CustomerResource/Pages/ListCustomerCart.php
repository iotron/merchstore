<?php

namespace App\Filament\Resources\Customer\CustomerResource\Pages;

use App\Filament\Resources\Customer\CustomerResource;
use App\Models\Category\Category;
use App\Models\Customer\Customer;
use App\Models\Product\Product;
use BaconQrCode\Common\Mode;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\Concerns\UsesResourceForm;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\Page;
use Filament\Resources\Table;

use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ListCustomerCart extends ListRecords
{

    protected static string $resource = CustomerResource::class;



    protected static ?string $title = 'Customer Cart';
    public $customerId;
    public $customer;






    public function mount($record=''): void
    {
        $this->customerId = $record;
        $this->customer = Customer::find($record);
        $this->record = $this->customer;
    }


    public function getTableQuery(): Builder
    {
        return $this->customer->cart()->getQuery();
    }

    public  function table(Table $table): Table
    {

        return $table
            ->columns([

                TextColumn::make('product')->label(__('Product'))->formatStateUsing(function ($record){
                    return $record->name;
                }),

                TextColumn::make('quantity'),
                TextColumn::make('discount'),

            ])
            ->actions([])
            ->filters([]);


    }



    protected function getTableActions(): array
    {
        return array_merge(parent::getTableActions(),[

            \Filament\Tables\Actions\Action::make('cleanup')->action('hezo'),

            DeleteAction::make(),

        ]);
    }

    public function hezo()
    {
        dd("Hellozzz");
    }






    // Add Product To Cart

    protected function getActions(): array
    {
        return [
            Action::make('addProduct')
                ->label(__('Add To Cart'))
                ->action(function (array $data): void {
                    if ($data['stock'] >= $data['quantity'])
                    {
                        $this->customer->cart()->attach($data['product_id'],['quantity' => $data['quantity']]);
                    }

                })
                ->form($this->customPOSForm())

        ];
    }




    public function customPOSForm()
    {
        return [

            Select::make('category_id')
                ->options(Category::notParents()->where('status','=',true)->whereHas('products',function ($q){ $q->where('status','=',Product::PUBLISHED);})
                    ->with(['products' => function($q){ return $q->where('status','=',Product::PUBLISHED);}])
                    ->orderBy('name')->pluck('name','id'))
                ->lazy()
                ->afterStateUpdated(function (\Closure $set,$state){
                    $set('product_id','');
                })
                ->label(__('Select Category')),


            Select::make('product_id')
                ->options(function (\Closure $get){
                    if (!empty($get('category_id')))
                    {
                        $selectedCategory = Category::with('products')->firstWhere('id','=',$get('category_id'));
                        $products = $selectedCategory->products;
//                        return $products->map(function ($item) {
//                            return [$item->id] = $item->name;
//                        });
                        return $products->pluck('name','id');
                    }
                    return [];
                })
                ->label(__('Select Product'))
                ->lazy()
                ->afterStateUpdated(function ($state,\Closure $set){
                    $selectedProduct = Product::firstWhere('id',$state);
                    $set('stock',$selectedProduct->availableStocks()->sum('in_stock_quantity'));
                    $set('sku',$selectedProduct->sku);
//                    $set('price',$selectedProduct->price->getAmount());
                    $set('formatted_price',$selectedProduct->price->formatted());
                })
                ->visible(function (\Closure $get){
                    return !empty($get('category_id'));
                }),



                Fieldset::make('product_details')
                    ->label(__('Bill Details'))
                    ->schema([
                        TextInput::make('stock')->inlineLabel()->disabled(),
                        TextInput::make('sku')->inlineLabel()->disabled(),
//                        TextInput::make('price')->inlineLabel(),
                        TextInput::make('formatted_price')->label(__('Price'))->inlineLabel()->disabled(),


                        TextInput::make('quantity')
                            ->numeric()
                            ->inlineLabel()
                            ->minValue(1)
                            ->maxValue(999999999)
                            ->lazy()
                            ->afterStateUpdated(function (\Closure $get, \Closure $set, $state){
                                $stockCount = $get('stock');
                                if ($state <= $stockCount)
                                {
                                    $product = Product::firstWhere('id',$get('product_id'));
                                    $total = $product->price->multiplyOnce($state);
                                    $set('total',$total->getAmount());
                                    $set('formatted_total',$total->formatted());
                                }else{
                                    $set('formatted_total','Error');
                                }
                            })
                            ->helperText(function (\Closure $get){
                                return empty($get('stock')) ? 'no stock available' : 'available stock :'.$get('stock');
                            })
                            ->disabled(function (\Closure $get){
                                return empty($get('stock'));
                            })
                            ->required(),


                        TextInput::make('total')->disabled()->inlineLabel()->hidden(),
                        TextInput::make('formatted_total')->label(__('Total'))->disabled()->inlineLabel(),



                    ])->visible(function (\Closure $get){
                        return !empty($get('product_id'));
                    })->columns(1),





        ];
    }





    public function getProductSelectOption()
    {
        return $this->categoryBag->mapWithKeys(
            function ($item) {
                return [$item['id'] => $item['name'].' - '.$item['desc']];
            }
        )->toArray();
    }












}
