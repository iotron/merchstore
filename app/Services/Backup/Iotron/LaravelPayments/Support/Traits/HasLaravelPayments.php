<?php

namespace App\Services\Iotron\LaravelPayments\Support\Traits;

trait HasLaravelPayments
{
    public function getTransactionId(): ?string
    {
        return $this->provider_transaction_id;
    }
}
