<?php

namespace App\Services\Iotron\LaravelPayments;


use App\Models\Payment\PaymentProvider;
use Illuminate\Database\Eloquent\Collection;

class LaravelPayments
{
    protected ?Collection $paymentProviders = null;

    protected array $providers = [];

    protected ?object $activeProvider = null;

    protected bool $useDB = false;

    public function __construct()
    {
        $this->resolveAvailableProviders();
    }

    public static function make(?string $provider = null): object
    {
        $serviceInstance = app(LaravelPayments::class);

        return is_null($provider) ? $serviceInstance->get() : $serviceInstance->switch($provider)->get();
    }

    public static function provider(string $provider): static|LaravelPayments
    {
        $serviceInstance = app(LaravelPayments::class);
        $serviceInstance->switch($provider);

        return $serviceInstance;
    }

    public function switch(string $provider): static
    {
        $this->switchProvider($provider);

        return $this;
    }

    public function get()
    {
        return $this->activeProvider;
    }

    private function switchProvider(string $provider): void
    {
        $this->activeProvider = $this->providers[$provider];
    }

    private function resolveAvailableProviders(): void
    {
        $availableProviders = PaymentProvider::where('status', true)->get();
        $this->useDB = config('laravel-payments.active.database');
        $allowedProviders = $this->useDB ? config('laravel-payments.active.providers') : $availableProviders->pluck('url')->toArray();
        $this->paymentProviders = $availableProviders->whereIn('url', $allowedProviders);

        foreach ($this->paymentProviders as $paymentProvider) {
            $this->activateProvider($paymentProvider);
        }

        $defaultProvider = $this->useDB ? config('laravel-payments.active.default') : $this->paymentProviders->firstWhere('is_primary', true)->url;
        $this->switchProvider($defaultProvider);

    }

    private function activateProvider(PaymentProvider $paymentProvider): void
    {
        $configuration = config("laravel-payments.providers.{$paymentProvider->url}");
        if ($this->useDB) {
            $auth = [
                'key' => $paymentProvider->key,
                'secret' => $paymentProvider->secret,
            ];
        } else {
            $auth = [
                'key' => config("laravel-payments.providers.{$paymentProvider->url}.key"),
                'secret' => config("laravel-payments.providers.{$paymentProvider->url}.secret"),
            ];
        }

        // Initiate And Fill In Providers
        $this->providers[$paymentProvider->url] = new $configuration['class']($paymentProvider, $auth);

    }
}
