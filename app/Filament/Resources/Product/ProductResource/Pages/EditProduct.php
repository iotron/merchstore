<?php

namespace App\Filament\Resources\Product\ProductResource\Pages;

use App\Filament\Resources\Product\ProductResource;
use App\Helpers\Money\Money;
use App\Helpers\ProductHelper\Support\Attributes\AttributeHelper;
use App\Models\Category\Category;
use App\Models\Product\Product;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{

    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }



    public function mount(int|string $record): void
    {$this->record = $this->resolveRecord($record);
        $product = $this->record->toArray();
        // Check Money Instances
        $product['base_price'] = $product['base_price']->getAmount();
        $product['price'] = $product['price']->getAmount();


        // Add Product Flat Too
        $productFlat = $this->record->flat->toArray();
        parent::mount($record);
        // Fill Form With Data
        $this->form->fill(array_merge($product, $productFlat));

    }




    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([


                Section::make('General')->schema([

                    TextInput::make('sku')
                        ->label(__('SKU'))->helperText('Stock Keeping Unit (Unique) for the product')
                        ->hint('Max - 100')
                        ->maxLength(100)
                        ->required(),
                    TextInput::make('name')
                        ->label(__('Name'))
                        ->helperText('Name of the product')
                        ->hint('Max - 100')
                        ->maxLength(100)
                        ->required(),
                    TextInput::make('url')
                        ->label(__('Url'))
                        ->prefix(fn () => config('project.client_url').'/product/')
                        ->helperText('Url of the product')
                        ->hint('Max - 100')
                        ->maxLength(100)
                        ->required(),
                    Toggle::make('visible_individually')->label(__('Visibility'))
                        ->helperText('Visible on '.config('project.client_url'))->required(),

                    Fieldset::make(__('Manage'))->schema([
                        Toggle::make('featured')
                            ->label(__('Featured'))
                            ->required(),
                        Select::make('status')
                            ->label(__('Status'))
                            ->options(Product::StatusOptions)
                            ->default(Product::DRAFT)
                            ->selectablePlaceholder(false)->required(),
                    ])->columns(3),

                ]),



                Section::make('Description')->schema([

                    Textarea::make('short_description')
                        ->label(__(' Short Description'))
                        ->hint('Max - 255')
                        ->maxLength(255)
                        ->required(),
//                TiptapEditor::make('description')
//                    ->label(__('Long Description'))
//                    ->hint('Max - 2000')
//                    ->maxLength(2000)
//                    ->required(),

                ]),






                Section::make('Product Pricing')
                    ->schema([
                        TextInput::make('base_price')
                            ->columnSpan(2)
                            ->label(__('Base Price'))
                            ->lazy()
//                        ->mask(
//                            fn (TextInput\Mask $mask) => $mask->numeric()
//                                ->decimalPlaces(2)
//                                ->decimalSeparator('.')
//                                ->minValue(1)
//                                ->maxValue(99999999)
//                                ->thousandsSeparator(',')
//                        )
                            ->afterStateHydrated(function (TextInput $component,$state){
                                if($state instanceof Money)
                                {
                                    $component->state($state->getAmount());
                                }
                                return $state;
                            })
                            ->afterStateUpdated(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get, $state) {
                                $basePrice = new Money($state);
                                $taxPercent = $get('tax_percent');
                                $this->calculate($basePrice,$taxPercent,$set,$get);
                            })
                            ->hint('enter value multiply by 100')
                            ->default(0.00)
                            ->columnSpan(2)
                            ->required(),


                        TextInput::make('price')
                            ->disabled(),


                        TextInput::make('formatted_total')
                            ->label(__('Formatted Total'))
                            ->formatStateUsing(function (\Filament\Forms\Get $get){
                                $priceAmount = $get('price');
                                if($priceAmount instanceof Money)
                                {
                                    return $priceAmount->formatted();
                                }else{
                                    if (!empty($priceAmount))
                                    {
                                        $result = new Money($priceAmount);
                                        return $result->formatted();
                                    }else{
                                        return 0.00;
                                    }
                                }
                            })->disabled(),

                    ])->columns(2),





                Section::make('Tax Calculation')
                    ->schema([
                        TextInput::make('hsn_code')->columnSpanFull()->maxLength(50)->hint(__('Max: 50')),
                        TextInput::make('tax_percent')
                            ->lazy()
                            ->afterStateUpdated(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get, $state) {
                                $taxPercent = $state;
                                $basePrice = new Money($get('base_price'));
                                $this->calculate($basePrice,$taxPercent,$set,$get);
                            }),
                        TextInput::make('tax_amount')
                            ->disabled(),

                    ])->columns(2),







                Section::make('Allocation Per Customer')->schema([
                    TextInput::make('min_range')
//                    ->mask(
//                        fn (TextInput\Mask $mask) => $mask
//                            ->numeric()
//                            ->decimalPlaces(2) // Set the number of digits after the decimal point.
//                            ->decimalSeparator('.') // Add a separator for decimal numbers.
//                            ->minValue(1)
//                            ->maxValue(10)
//                    )
                        ->default(1),
                    TextInput::make('max_range')
//                    ->mask(
//                        fn (TextInput\Mask $mask) => $mask
//                            ->numeric()
//                            ->decimalPlaces(2) // Set the number of digits after the decimal point.
//                            ->decimalSeparator('.') // Add a separator for decimal numbers.
//                            ->minValue(1)
//                            ->maxValue(10)
//                    )
                        ->default(1),
                ])->columns(2),




                Section::make('Shipping')->schema([

                    TextInput::make('length')
                        ->label(__('Length'))
                        ->placeholder('Length in CMs')
                        ->hint('Enter decimal in Unit CM')
                        ->required(),
                    TextInput::make('width')
                        ->label(__('Width'))
                        ->placeholder('width in CMs')
                        ->hint('Enter decimal in Unit CM')
                        ->required(),
                    TextInput::make('height')
                        ->label(__('Height'))
                        ->placeholder('Height in CMs')
                        ->hint('Enter decimal in Unit CM')
                        ->required(),
                    TextInput::make('weight')
                        ->label(__('Weight'))->placeholder('weight in KGs')
                        ->hint('Enter decimal in Unit KG')
                        ->required(),

                ])->columns(2),





                Section::make('Media')->schema([

                    SpatieMediaLibraryFileUpload::make('productDisplay')
                        ->multiple()
                        ->collection('productDisplay')
                        ->reorderable(),

                    SpatieMediaLibraryFileUpload::make('productGallery')
                        ->multiple()
                        ->collection('productGallery')
                        ->columnSpan(2)
                        ->reorderable(),
                ])->columns(3),



                Section::make('Product Details')
                    ->schema(array_merge([
                        Select::make('categories')
                            ->relationship('categories', 'name', function ($query) {
                                return $query->notParents()->select('id', 'name', 'desc')->where('status','=',true)->orderBy('name');
                            })
                            ->getOptionLabelFromRecordUsing(fn (Category $record) => "{$record->name} - {$record->desc}")
                            ->multiple()
                            ->placeholder(__('Select Categories'))
                            ->required(),
                    ],$this->productAttributeSchema())),





            ]);
    }


    private function productAttributeSchema(): array
    {
        return (new AttributeHelper())->getProductAttributes($this->record->attribute_group_id);
    }



}
