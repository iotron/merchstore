<?php

namespace App\Filament\Resources\Product\ProductResource\Pages;

use App\Filament\Resources\Product\ProductResource;

use App\Models\Enums\Product\ProductTypeCast;
use App\Services\MoneyServices\Money;
use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function getRelationManagers(): array
    {
        $relationManagers [] = ProductResource\RelationManagers\AllStocksRelationManager::class;
        if ($this->record->type == ProductTypeCast::CONFIGURABLE)
        {
            $relationManagers [] = ProductResource\RelationManagers\VariantsRelationManager::class;
        }
        return $relationManagers;
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->record->load('flat');
        $product = $this->record->toArray();

        // Add Product Flat Too
        $productFlat = $this->record->flat->toArray();
        // Fill Form With Data
        $this->form->fill(array_merge($product, $productFlat));

    }


    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([

                Infolists\Components\Tabs::make('Tabs')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('General')
                            ->schema([
                                Infolists\Components\Section::make('General Info')
                                    ->aside()
                                    ->columns(2)
                                    ->schema([

                                        Infolists\Components\TextEntry::make('name'),
                                        Infolists\Components\TextEntry::make('sku'),
                                        Infolists\Components\TextEntry::make('url')
                                            ->prefix(fn () => config('project.client_url').'/product/')
                                            ->helperText('Url of the product')
                                            ->suffixAction(Infolists\Components\Actions\Action::make('visit')
                                                ->icon('heroicon-o-globe-alt')
                                                ->iconButton()
                                                ->url(fn () => config('project.client_url').'/product/'.$this->record->url,true)
                                            ),
                                        Infolists\Components\TextEntry::make('price')->money(Money::defaultCurrency()),

                                        Infolists\Components\Fieldset::make('Return Details')
                                            ->columns()
                                            ->schema([
                                               Infolists\Components\IconEntry::make('is_returnable')
                                                   ->label(__('Returable Product'))
                                                   ->inlineLabel()
                                                   ->default(false)->boolean(),
                                                Infolists\Components\TextEntry::make('return_window')
                                                    ->label(__('Period'))
                                                    ->visible(fn() => $this->record->is_returnable)
                                                    ->inlineLabel()
                                                    ->since()

                                            ]),



                                        Infolists\Components\Fieldset::make('Manage')
                                            ->columns(2)
                                            ->schema([

                                                Infolists\Components\IconEntry::make('featured')
                                                    ->default(false)->boolean()->inlineLabel()
                                                    ->alignCenter()->label('Featured'),

                                                Infolists\Components\TextEntry::make('status')->badge()->inlineLabel(),

                                            ]),


                                    ]),
                            ]),
                        Infolists\Components\Tabs\Tab::make('Media')
                            ->columns(3)
                            ->schema([

                                Infolists\Components\SpatieMediaLibraryImageEntry::make('displayImage')
                                    ->columnSpan(1)
                                    ->size('50%')
                                    ->alignCenter()
                                    ->extraImgAttributes(['class' => 'rounded-xl mx-auto'])
                                    ->collection('productDisplay'),

                                Infolists\Components\SpatieMediaLibraryImageEntry::make('bannerImage')
                                    ->columnSpan(2)
                                    ->size('50%')
                                    ->extraImgAttributes(['class' => 'rounded-xl mx-auto'])
                                    ->collection('productGallery'),


                            ]),
                        Infolists\Components\Tabs\Tab::make('About')
                            ->schema([
                                Infolists\Components\Section::make('About Product')
                                    ->aside()
                                    ->relationship('flat')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('short_description')
                                            ->label(__('Short Description'))
                                            ->default('--not provided yet--')
                                            ->alignJustify()->columnSpanFull(),
                                        Infolists\Components\TextEntry::make('description')
                                            ->alignJustify()->columnSpanFull()->html()
                                        ->default('--not provided yet--')
                                    ])
                            ]),
                        Infolists\Components\Tabs\Tab::make('Pricing & Tax')
                            ->schema([

                                Infolists\Components\Section::make([

                                    Infolists\Components\TextEntry::make('hsn_code')
                                        ->default('--not provided yet--')
                                        ->label(__('HSN Code')),

                                    Infolists\Components\TextEntry::make('tax_percent')
                                        ->label(__('Tax Percentage'))->suffix('%'),

                                    Infolists\Components\TextEntry::make('price')
                                        ->label(__('Price'))
                                        ->default(0)
                                        ->money(Money::defaultCurrency()),
                                ])->columns(2)


                            ]),
                        Infolists\Components\Tabs\Tab::make('Allocation')
                            ->schema([
                                Infolists\Components\Section::make('Allocation Per Customer')
                                    ->aside()
                                    ->schema([
                                        Infolists\Components\TextEntry::make('min_range'),
                                        Infolists\Components\TextEntry::make('max_range'),
                                    ])
                            ]),
                        Infolists\Components\Tabs\Tab::make('Shipping')
                            ->schema([
                                Infolists\Components\Section::make([
                                    Infolists\Components\TextEntry::make('length'),
                                    Infolists\Components\TextEntry::make('width'),
                                    Infolists\Components\TextEntry::make('height'),
                                    Infolists\Components\TextEntry::make('weight'),
                                ])->columns(2)->relationship('flat'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('Attributes')
                            ->schema([

                                Infolists\Components\RepeatableEntry::make('categories')
                                    ->schema([Infolists\Components\TextEntry::make('name')])
                                    ->grid(4),


                                Infolists\Components\RepeatableEntry::make('filterOptions')
                                    ->label(__('Filter Options'))
                                    ->schema([Infolists\Components\TextEntry::make('display_name')])
                                    ->grid(4),




                            ]),
                    ])->columnSpanFull()->contained(false),








            ]);
    }


}
