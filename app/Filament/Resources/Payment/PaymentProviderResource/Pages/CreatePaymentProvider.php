<?php

namespace App\Filament\Resources\Payment\PaymentProviderResource\Pages;

use App\Filament\Resources\Payment\PaymentProviderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentProvider extends CreateRecord
{
    protected static string $resource = PaymentProviderResource::class;
}
