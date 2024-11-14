<?php

namespace App\Filament\Resources\Product\ProductResource\Pages;

use App\Filament\Resources\Product\ProductResource;
use App\Helpers\ProductHelper\PriceCalculator;
use App\Helpers\ProductHelper\Support\Attributes\AttributeHelper;
use App\Models\Category\Category;
use App\Models\Enums\Product\ProductStatusCast;
use App\Models\Enums\Product\ProductTypeCast;
use App\Models\Product\Product;
use App\Services\MoneyServices\Money;
use Awcodes\Shout\Components\Shout;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\HtmlString;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Save')->action('save')->color('success'),
            Action::make('Sync Stock')->label('Sync Stock')->action('syncStock')->color('primary'),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    public function getRelationManagers(): array
    {
        $relationManagers[] = ProductResource\RelationManagers\AllStocksRelationManager::class;
        if ($this->record->type == ProductTypeCast::CONFIGURABLE) {
            $relationManagers[] = ProductResource\RelationManagers\VariantsRelationManager::class;
        }
        return $relationManagers;
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->record->load('flat');
        $data = $this->record->toArray();
        $filterOption = collect($this->record->filterOptions)->flatMap(function ($option) {
            return [
                $option->filter->display_name => $option->id,
            ];
        });
        $data['filter_options'] = $filterOption->toArray();

        $this->form->fill($data);


        // dd($this->data,$this->record->toArray());
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $data = $this->form->getState();

        $validator = true;
        if ($this->record->sku != $data['sku']) {
            $validator = Validator::make($this->form->getState(), [
                'sku' => 'required|unique:products|max:255',
            ]);
        } else {
            unset($data['sku']);
        }
        if ($validator) {
            // Get And Set Product Type Instance/Class From App/Types
            $typeInstance = app(config('project.product_types.' . $this->record->type->value . '.class'));
            // Create Product From App\Type Class->create
            $product = $typeInstance->update($this->record->id, $data);
            $product->save();
            // $this->notify('success', 'You have successfully modify product details', isAfterRedirect: true);
            $this->getSavedNotification()?->send();
        }
    }

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema($this->getFormSchema());
    }

    private function productAttributeSchema(): array
    {
        return (new AttributeHelper())->getProductAttributes($this->record->filter_group_id);
    }


    public function getFormSchema(): array
    {
        return [

            Forms\Components\Tabs::make('Tabs')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')
                        ->schema([
                            Forms\Components\Section::make('Primary')
                                ->aside()->description('Primary details about the product')
                                ->schema([

                                    Forms\Components\TextInput::make('sku')
                                        ->label(__('SKU'))->helperText('Stock Keeping Unit (Unique) for the product')
                                        ->hint('Max - 100')
                                        ->maxLength(100)
                                        ->required(),

                                    Forms\Components\TextInput::make('name')
                                        ->label(__('Name'))
                                        ->helperText('Name of the product')
                                        ->hint('Max - 100')
                                        ->maxLength(100)
                                        ->required(),
                                    Forms\Components\TextInput::make('url')
                                        ->label(__('Url'))
                                        ->prefix(fn() => config('project.client_url') . '/product/')
                                        ->helperText('Url of the product')
                                        ->hint('Max - 100')
                                        ->maxLength(100)
                                        ->required(),


                                    Forms\Components\Fieldset::make(__('Manage'))->schema([

                                        Toggle::make('featured')
                                            ->label(__('Featured'))
                                            ->required(),
                                        Forms\Components\Select::make('status')
                                            ->label(__('Status'))
                                            ->inlineLabel()
                                            ->options(collect(ProductStatusCast::cases())
                                                ->mapWithKeys(fn(ProductStatusCast $status) => [$status->value => $status->getLabel()])
                                                ->toArray())
                                            ->default(ProductStatusCast::DRAFT->value)
                                            ->selectablePlaceholder(false)->required(),
                                    ])->columns(3),

                                ]),

                            Forms\Components\Section::make('Price')
                                ->aside()->description('Price details about the product')
                                ->schema([
                                    Forms\Components\TextInput::make('price')
                                        ->columnSpan(2)
                                        ->label(__('Base Price'))
                                        ->lazy()
                                        ->numeric()
                                        ->inputMode('decimal')
                                        ->default(0.00)
                                        ->minValue(0)
                                        ->maxValue(99999999)
                                        ->required()
                                        ->lazy()
                                        ->extraInputAttributes(['step' => '0.01', 'min' => 0, 'max' => 99999999])
                                        ->hint('enter value multiply by 100')
                                        ->default(0.00)
                                        ->columnSpan(2)
                                        ->required(),

                                    Forms\Components\TextInput::make('hsn_code')
                                        ->columnSpanFull()
                                        ->maxLength(50)->hint(__('Max: 50')),
                                    Forms\Components\TextInput::make('tax_percent')
                                        ->lazy(),
                                    Shout::make('pricingInfo')
                                        ->color('info')
                                        ->content(fn(Get $get) => $this->getShoutContent($get)),
                                ]),


                        ]),
                    Forms\Components\Tabs\Tab::make('Media')
                        ->schema([
                            Forms\Components\Section::make('Media')->schema([

                                SpatieMediaLibraryFileUpload::make('productDisplay')
                                    ->multiple()
                                    ->collection('productDisplay')
                                    ->imageEditor()
                                    ->reorderable(),

                                SpatieMediaLibraryFileUpload::make('productGallery')
                                    ->multiple()
                                    ->collection('productGallery')
                                    ->columnSpan(2)
                                    ->reorderable(),
                            ])->columns(3),
                        ]),
                    Forms\Components\Tabs\Tab::make('About')
                        ->schema([
                            Forms\Components\Section::make('Description')->schema([

                                Forms\Components\Textarea::make('flat.short_description')
                                    ->label(__(' Short Description'))
                                    ->hint('Max - 255')
                                    ->maxLength(255)
                                    ->required(),
                                TiptapEditor::make('flat.description')
                                    ->label(__('Long Description'))
                                    ->hint('Max - 2000')
                                    //->maxLength(2000)
                                    ->output(TiptapOutput::Html)
                                    ->required(),

                            ]),
                        ]),

                    Forms\Components\Tabs\Tab::make('Policies')
                        ->schema([

                            Toggle::make('is_returnable')
                                ->label(__('Returnable'))
                                ->helperText(__('customers have the option to return this product'))
                                ->inlineLabel()
                                ->lazy()
                                ->default(false),

                            Forms\Components\DateTimePicker::make('return_window')
                                ->seconds(false)
                                ->time(false)
                                ->label(__('Cancellation Period'))
                                ->minDate($this->record->created_at)
                                ->inlineLabel()
                                ->visible(function (Get $get) {
                                    return $get('is_returnable');
                                }),
                        ]),

                    Forms\Components\Tabs\Tab::make('Allocation')
                        ->schema([
                            Forms\Components\Section::make('Allocation Per Customer')
                                ->schema([
                                    Forms\Components\TextInput::make('min_range')->default(1),
                                    Forms\Components\TextInput::make('max_range')->default(1),
                                ])
                                ->columns(2),
                        ]),

                    Forms\Components\Tabs\Tab::make('Shipping')
                        ->schema([
                            Forms\Components\Section::make('Shipping')
                                ->schema([

                                    Forms\Components\TextInput::make('flat.length')
                                        ->label(__('Length'))
                                        ->placeholder('Length in CMs')
                                        ->hint('Enter decimal in Unit CM')
                                        ->required(),
                                    Forms\Components\TextInput::make('flat.width')
                                        ->label(__('Width'))
                                        ->placeholder('width in CMs')
                                        ->hint('Enter decimal in Unit CM')
                                        ->required(),
                                    Forms\Components\TextInput::make('flat.height')
                                        ->label(__('Height'))
                                        ->placeholder('Height in CMs')
                                        ->hint('Enter decimal in Unit CM')
                                        ->required(),
                                    Forms\Components\TextInput::make('flat.weight')
                                        ->label(__('Weight'))->placeholder('weight in KGs')
                                        ->hint('Enter decimal in Unit KG')
                                        ->required(),

                                ])
                                ->columns(2),
                        ]),

                    Forms\Components\Tabs\Tab::make('Attributes')
                        ->schema([
                            Forms\Components\Section::make('Product Details')
                                ->schema(array_merge([
                                    Forms\Components\Select::make('categories')
                                        ->relationship('categories', 'name', function ($query) {
                                            return $query->notParents()->select('id', 'name', 'desc')->where('status', '=', true)->orderBy('name');
                                        })
                                        ->getOptionLabelFromRecordUsing(fn(Category $record) => "{$record->name} - {$record->desc}")
                                        ->multiple()
                                        ->placeholder(__('Select Categories'))
                                        ->required(),
                                ], $this->productAttributeSchema())),
                        ]),
                ])->columnSpanFull()->contained(false),



        ];
    }





    public function getShoutContent(Forms\Get $get): HtmlString
    {
        $priceValue = $get('price') ?? 0;
        $taxPercentage = $get('tax_percent') ?? 0;
        $data = PriceCalculator::calculatePrices($priceValue, $taxPercentage);
        return new HtmlString('
            <span>Base Price : </span>' . Money::format($data['base_price']) . ' |
            <span>Tax : </span>' . Money::format($data['tax']) . ' |
            <span>Final Price : </span>' . Money::format($data['final_price']) . '
        ');
    }




}
