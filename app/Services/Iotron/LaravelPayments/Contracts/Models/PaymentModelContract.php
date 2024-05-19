<?php

namespace App\Services\Iotron\LaravelPayments\Contracts\Models;

interface PaymentModelContract
{
    public function getTransactionId(): ?string;
}
