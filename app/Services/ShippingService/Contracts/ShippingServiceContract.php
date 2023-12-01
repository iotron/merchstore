<?php

namespace App\Services\ShippingService\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ShippingServiceContract
{

    public function provider(?string $provider = null): ShippingProviderContract;
    public function switch(string $provider): ShippingProviderContract;
    public function getProviderName():string;
    public function allProviders():array;
    public function getAllProvidersModel(): ?Collection;

    public function getProviderModel():Model;
    public function setError(string $error): void;
    public function getError(): ?string;



}
