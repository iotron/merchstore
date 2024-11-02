<?php

namespace App\Services\Iotron\LaravelPayments\Support\Traits;

trait HasLaravelPaymentProviders
{
    public function isPrimary(): bool
    {
        return $this->is_primary;
    }
}
