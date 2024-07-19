<?php

namespace App\Filament\Resources\Product\ProductResource\Pages;

use App\Filament\Resources\Product\ProductResource;
use App\Services\Iotron\MoneyService\Money;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
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
                Section::make('Product Information')
                    ->aside()
                    ->columns(2)
                    ->schema([

                        TextEntry::make('name'),
                        TextEntry::make('sku'),
                        TextEntry::make('price')->money(Money::defaultCurrency()),

                    ]),

                Section::make('Description')
                    ->aside()
                    ->schema([
                        TextEntry::make('flat.description')->hiddenLabel()->alignJustify()->columnSpanFull()->html()
                    ])

            ]);
    }


}
