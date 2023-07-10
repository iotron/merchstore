<?php

namespace App\Filament\Resources\Promotion\SaleResource\Pages;

use App\Filament\Resources\Promotion\SaleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSale extends ViewRecord
{
    protected static string $resource = SaleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
