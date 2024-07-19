<?php

namespace App\Filament\Resources\Promotion\SaleResource\Pages;

use App\Filament\Resources\Promotion\SaleResource;
use App\Helpers\Promotion\Sales\ProductSaleHelper;
use App\Helpers\Promotion\Sales\SaleHelper;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditSale extends EditRecord
{
    protected static string $resource = SaleResource::class;

    private $saleHelper;

    public $conditions;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $sale = $this->record->toArray();


        $this->saleHelper = new SaleHelper();
        $this->conditions = $this->saleHelper->getCondition();
        $this->form->fill(array_merge($sale));
        // $this->fillForm();
    }

    protected function afterSave()
    {
        $saleProducts = new ProductSaleHelper();
        $saleProducts->reindexSaleableProducts();
    }

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema($this->getFormSchema());
    }

    public function getConditionField(array $attribute = [])
    {
        if (! empty($attribute)) {
            return match ($attribute['type']) {
                'select' => Select::make('value')
                    ->label('Value')
                    ->options(function () use ($attribute) {
                        return $attribute['options'];
                    })->required(),
                'multiselect' => Select::make('value')->multiple()->label('Value')
                    ->options(function () use ($attribute) {
                        return $attribute['options'];
                    })->required(),
                default => TextInput::make('value')
                    ->type(function () use ($attribute) {
                        return $attribute['options'] ?? 'text';
                    })->placeholder(function () use ($attribute) {
                        return 'Enter '.$attribute['label'];
                    })->required(),
            };
        } else {
            return [];
        }
    }

    protected function getFormSchema(): array
    {
        return [

            Fieldset::make('General')
                ->schema([

                    TextInput::make('name')
                        ->label(__('Rule Name'))
                        ->placeholder(__('Enter Rule Name'))
                        ->required()
                        ->columnSpan(2)
                        ->hint(__('Max: 250'))
                        ->maxLength(250),

                    Select::make('customerGroup')
                        ->label('Customer Groups')
                        ->multiple()
                        ->relationship('customer_groups', 'name')
                        ->placeholder(__('Select some groups'))
                        ->required(),

                    Textarea::make('description')
                        ->placeholder('Write Briefly About This Rule')
                        ->hint(__('Max: 40,000'))
                        ->columnSpanFull()
                        ->maxLength(40000),

                    Toggle::make('status')->inline(),
                ])->columns(3),

            Fieldset::make('Rule Information')
                ->schema([
                    DateTimePicker::make('starts_from')->required()->placeholder('Set Start Date And Time'),
                    DateTimePicker::make('ends_till')->required()->placeholder('Set End Date And Time'),
                    TextInput::make('sort_order')->type('number')->label('Priority')->required()->placeholder('Set Priority'),
                ])->columns(3),

            Fieldset::make('Discount Information')
                ->schema([
                    Select::make('action_type')->options([
                        'by_percent' => 'Percentage of Product Price',
                        'by_fixed' => 'Fixed Amount',
                    ])->required()->label('Discount Type'),
                    TextInput::make('discount_amount')->label('Discount Amount')->required()->placeholder('Enter Discount'),

                    Select::make('end_other_rules')->options([
                        0 => 'No',
                        1 => 'Yes',
                    ])->required(),
                ])->columns(3),

            Fieldset::make('Conditions_list')
                ->schema([

                    Select::make('condition_type')
                        ->label(__('Condition Type'))
                        ->options([
                            0 => 'Match All Conditions',
                            1 => 'Match Any Condition',
                        ])
                        ->placeholder(__('Select condition type'))
                        ->columnSpanFull()
                        ->required(),

                    Repeater::make('conditions')
                        ->label(__('Condition List'))
                        ->schema([
                            Select::make('attribute')
                                ->label(__('Choose Attribute'))
                                ->options($this->conditions->pluck('label', 'key')->toArray())
                                ->placeholder(__('Select an attribute'))
                                ->columnSpan(function ($state) {
                                    return empty($state) ? 3 : 1;
                                })
                                ->lazy(),

                            Fieldset::make('options')
                                ->label(__('Options Details'))
                                ->schema(function (callable $get) {
                                    if ($get('attribute') !== null) {
                                        // $conditionList = $this->getCondition();
                                        $item = $this->conditions->where('key', $get('attribute'))->first();

                                        if (! empty($item)) {
                                            $field = $this->getConditionField($item);
                                        } else {
                                            $field = [];
                                        }

                                        // return $item['operator'];
                                        return [Select::make('operator')->options($item['operator']), $field];
                                    } else {
                                        return [];
                                    }
                                })
                                ->visible(function (\Filament\Forms\Get $get) {
                                    return ! empty($get('attribute'));
                                })
                                ->columnSpan(2),

                        ])
                        ->columns(3)
                        ->columnSpanFull()
                        ->collapsible(false),

                ])->columns(2),

        ];
    }
}
