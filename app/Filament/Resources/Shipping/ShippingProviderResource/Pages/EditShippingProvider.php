<?php

namespace App\Filament\Resources\Shipping\ShippingProviderResource\Pages;

use App\Filament\Resources\Shipping\ShippingProviderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShippingProvider extends EditRecord
{
    protected static string $resource = ShippingProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
