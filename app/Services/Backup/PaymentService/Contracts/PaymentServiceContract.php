<?php

namespace App\Services\PaymentService\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface PaymentServiceContract
{
    // public function provider(?string $provider=null):PaymentProviderContract;

    public function allProviders(): array;

    public function getProviderName(): string;

    public function getProviderModel(): Model;

    public function getAllProvidersModel(): ?Collection;

    public function setError(string $error): void;

    public function getError(): ?string;
}
