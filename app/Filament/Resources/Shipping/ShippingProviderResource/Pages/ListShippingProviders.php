<?php

namespace App\Filament\Resources\Shipping\ShippingProviderResource\Pages;

use App\Filament\Resources\Shipping\ShippingProviderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShippingProviders extends ListRecords
{
    protected static string $resource = ShippingProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
