<?php

namespace App\Services\ShippingService;

use App\Models\Shipping\ShippingProvider as ShippingProviderModel;
use App\Services\ShippingService\Contracts\ShippingProviderContract;
use App\Services\ShippingService\Contracts\ShippingServiceContract;
use App\Services\ShippingService\Providers\ShipRocket\ShipRocketApi;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ShippingService implements ShippingServiceContract
{
    protected ?ShippingProviderContract $provider = null;

    protected array $providers = [];

    protected ?Collection $allShippingProviders = null;

    public function __construct(...$provider_name)
    {
        // Enable All Available Payment Providers
        $this->activateProviders($provider_name);
    }

    public function provider(?string $provider = null): ShippingProviderContract
    {
        if (! is_null($provider)) {
            $this->provider = $this->providers[$provider] ?? null;
        }
        throw_if(is_null($this->provider), ! is_null($provider) ? $provider.' payment provider not found' : 'no primary payment provider found');

        return $this->provider;
    }

    public function switch(string $provider): ShippingProviderContract
    {
        return $this->provider($provider);
    }

    public function getProviderName(): string
    {
        return $this->provider->getProviderName();
    }

    public function allProviders(): array
    {
        return $this->providers;
    }

    public function getAllProvidersModel(): ?Collection
    {
        return $this->allShippingProviders;
    }

    public function getProviderModel(): Model
    {
        return $this->allShippingProviders->where('code', '=', $this->getProviderName())->first();
    }

    private function activateProviders(array $providers_name): void
    {
        // Check Database
        $this->allShippingProviders = ShippingProviderModel::where('status', true)->get();
        throw_unless($this->allShippingProviders->count(), 'no provider records  found in Database');
        $primaryProviderCount = $this->allShippingProviders->where('is_primary', true)->count();
        throw_if($primaryProviderCount > 1, 'Multiple primary payment provider found!');
        throw_if($primaryProviderCount < 1, 'No primary payment provider found!');

        // Activating Given Providers
        foreach ($providers_name as $provider) {
            $providerModel = $this->allShippingProviders->firstWhere('code', $provider);
            throw_unless($providerModel->exists, 'no provider records  found in Database for '.$provider);

            // Prepare Instance
            $providerClass = $providerModel->service_provider;
            $providerApi = null;
            if ($providerModel->has_api) {
                $providerApi = $this->getProviderApi($provider);
                throw_if(is_null($providerApi), 'no api data found for '.$provider);
            }

            // Activate And Get Provider Instance
            $providerInstance = new $providerClass($providerModel, $providerApi);
            throw_unless($providerInstance instanceof ShippingProviderContract, $providerClass.' must implement App\Services\ShippingService\Contracts\ShippingProviderContract');
            // Add Provider Service In Providers List
            $this->providers[$provider] = $providerInstance;
            // Set Default Primary Provider
            if ($providerModel->is_primary && is_null($this->provider)) {
                $this->provider = $providerInstance;
            }

        }

    }

    protected function getProviderApi(string $provider): ?ShipRocketApi
    {

        return match ($provider) {
            ShippingProviderModel::SHIPROCKET => new ShipRocketApi(config('services.shiprocket.key'), config('services.shiprocket.secret')),
            default => null,
        };
    }

    public function setError(string $error): void
    {
        $this->provider->setError($error);
    }

    public function getError(): ?string
    {
        return $this->provider->getError();
    }
}
