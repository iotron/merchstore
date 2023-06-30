<?php

namespace App\Filament\Resources\Customer\CustomerResource\Pages;

use App\Filament\Resources\Customer\CustomerResource;
use App\Models\Category\Category;
use App\Models\Customer\Customer;
use App\Models\Product\Product;
use Closure;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\Concerns\UsesResourceForm;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\Page;
use Filament\Resources\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomerCart extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = CustomerResource::class;
    protected static string $view = 'filament::resources.pages.list-records';
    public $customerId;
    public Customer $customer;



    public static function route(string $path): array
    {
        return [
            'class' => static::class,
            'route' => $path,
        ];
    }


    public static function getNavigationLabel(): string
    {
        return 'Customer Cart';
    }

    public function mount($record = null): void
    {
        abort_unless($record, 404);
        $this->customerId = $record;
        $this->customer = Customer::find($record);
    }


    /**
     * @return string
     */
    public static function getResource(): string
    {
        return self::$resource;
    }


//    protected function getBreadcrumbs(): array
//    {
//        $breadcrumbs[$this->getResource()::getUrl()] = 'Customer';
//        $breadcrumbs[$this->getResource()::getUrl('cart', ['record' => $this->customerId])] = $this->customerId;
//        $breadcrumbs[] = $this->getTableHeading();
//
//
//
//        return $breadcrumbs;
//    }



    public function getTableQuery(): Builder
    {
        return $this->customer->cart()->getQuery();
    }



    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('product')->label(__('Product'))->formatStateUsing(function ($record){
                return $record->name;
            }),

            TextColumn::make('quantity'),
            TextColumn::make('discount'),
        ];
    }


    protected function getTableHeading(): string | Closure | null
    {
        return 'Live Cart';
    }






    protected function getTableEmptyStateHeading(): ?string
    {
        return Str::of('No ')->append('Products')->append(' yet');
    }


    protected function getTableActions(): array
    {
        return [
            Action::make('cleanup')
            ->action(function (Model $record){
                dd($record);
            }),

//            EditAction::make()->using(function (Model $record, array $data): Model {
//
//               // $record->update($data);
//                return $record;
//            })->action(function ($record){
//                dd($record);
//            }),


        ];
    }


    // Add Product To Cart

    protected function getActions(): array
    {
        return [
            \Filament\Pages\Actions\Action::make('addProduct')
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


                Select::make('product_id')
                    ->label(__('Product'))
                    ->options(Product::where('status','=',Product::PUBLISHED)->get()->pluck('sku','id'))
                    ->searchable()
                    ->placeholder(__('select a product'))
                    ->searchPrompt('start typing product sku')
                    ->required(),


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
