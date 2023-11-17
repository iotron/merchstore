<?php

namespace App\Filament\Resources\Payment\PaymentProviderResource\Pages;

use App\Filament\Resources\Payment\PaymentProviderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentProvider extends EditRecord
{
    protected static string $resource = PaymentProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
