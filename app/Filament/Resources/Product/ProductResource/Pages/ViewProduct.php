<?php

namespace App\Filament\Resources\Product\ProductResource\Pages;

use App\Filament\Resources\Product\ProductResource;

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

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
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
                                        Infolists\Components\TextEntry::make('price')->money(Money::defaultCurrency()),

                                    ]),
                            ]),
                        Infolists\Components\Tabs\Tab::make('Media')
                            ->columns()
                            ->schema([

                                Infolists\Components\SpatieMediaLibraryImageEntry::make('displayImage')
                                    ->collection('productDisplay'),

                                Infolists\Components\SpatieMediaLibraryImageEntry::make('bannerImage')
                                    ->collection('productGallery'),


                            ]),
                        Infolists\Components\Tabs\Tab::make('About')
                            ->schema([
                                Infolists\Components\Section::make('Description')
                                    ->aside()
                                    ->schema([
                                        Infolists\Components\TextEntry::make('flat.description')->hiddenLabel()->alignJustify()->columnSpanFull()->html()
                                    ])
                            ]),
                        Infolists\Components\Tabs\Tab::make('Pricing & Tax')
                            ->schema([
                                // ...
                            ]),
                        Infolists\Components\Tabs\Tab::make('Allocation')
                            ->schema([
                                // ...
                            ]),
                        Infolists\Components\Tabs\Tab::make('Shipping')
                            ->schema([
                                // ...
                            ]),
                        Infolists\Components\Tabs\Tab::make('Attributes')
                            ->schema([
                                // ...
                            ]),
                    ])->columnSpanFull()->contained(false),








            ]);
    }


}
