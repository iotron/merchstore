<?php

namespace App\Filament\Resources\Product\ProductResource\Pages;

use App\Filament\Resources\Product\ProductResource;
use Filament\Actions\EditAction;
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
        // Check Money Instances
        $product['base_price'] = $product['base_price']->getAmount();
        $product['price'] = $product['price']->getAmount();


        // Add Product Flat Too
        $productFlat = $this->record->flat->toArray();
        // Fill Form With Data
        $this->form->fill(array_merge($product, $productFlat));

    }


}
