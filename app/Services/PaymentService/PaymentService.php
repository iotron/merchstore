<?php

namespace App\Services\PaymentService;

use App\Models\Payment\PaymentProvider as PaymentProviderModel;
use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\PaymentServiceContract;
use App\Services\PaymentService\Providers\Razorpay\RazorpayApi;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use Stripe\StripeClient as StripeApi;

/**
 * Core Payment Service Class
 * This Class Help Switch Between
 * Available Payment Providers
 */
class PaymentService implements PaymentServiceContract
{

    /**
     * @var PaymentProviderContract|null
     */
    protected ?PaymentProviderContract $provider=null;

    /**
     * @var array
     */
    protected array $providers=[];

    protected ?Collection $allPaymentProviders=null;

    /**
     * @param ...$provider_name
     */
    public function __construct(...$provider_name)
    {
        // Enable All Available Payment Providers
        $this->activateProviders($provider_name);
    }


    /**
     * Use Primary Payment Provider
     * Also Can Switch Between Providers
     * @param string|null $provider
     * @return PaymentProviderContract
     */
    public function provider(?string $provider = null): PaymentProviderContract
    {
        if (!is_null($provider))
        {
            $this->provider = $this->providers[$provider] ?? null;
        }
        throw_if(is_null($this->provider),!is_null($provider) ? $provider.' payment provider not found' : 'no primary payment provider found');
        return $this->provider;
    }

    /**
     * Switch Payment Provider
     * @param string $provider
     * @return PaymentProviderContract
     */
    public function switch(string $provider): PaymentProviderContract
    {
        return $this->provider($provider);
    }

    public function getProviderName():string
    {
        return $this->provider->getProviderName();
    }

    /**
     * Get All Payment Providers
     * @return array
     */
    public function allProviders():array
    {
        return $this->providers;
    }

    public function getAllProvidersModel(): ?Collection
    {
        return $this->allPaymentProviders;
    }

    public function getProviderModel():Model
    {
        return  $this->allPaymentProviders->where('code','=',$this->getProviderName())->first();
    }


    /**
     * Activate/Enable All Given Providers
     * @param array $providers_name
     * @return void
     */
    private function activateProviders(array $providers_name):void
    {
        // Check Database
        $this->allPaymentProviders = PaymentProviderModel::where('status',true)->get();
        throw_unless($this->allPaymentProviders->count(),'no provider records  found in Database');
        $primaryProviderCount = $this->allPaymentProviders->where('is_primary',true)->count();
        throw_if($primaryProviderCount > 1,'Multiple primary payment provider found!');
        throw_if($primaryProviderCount < 1,'No primary payment provider found!');

        // Activating Given Providers
        foreach ($providers_name as $provider)
        {
            if ($provider != PaymentProviderModel::RAZORPAYX)
            {
                $providerModel = $this->allPaymentProviders->firstWhere('code',$provider);
                throw_unless($providerModel->exists,'no provider records  found in Database for '.$provider );


                // Prepare Instance
                $providerClass = $providerModel->service_provider;
                $providerApi = null;
                if ($providerModel->has_api)
                {
                    $providerApi = $this->getProviderApi($provider);
                    throw_if(is_null($providerApi),'no api data found for '.$provider);
                }


                if ($provider == PaymentProviderModel::RAZORPAY && in_array(PaymentProviderModel::RAZORPAYX,$providers_name))
                {
                    // Activate And Get Provider Instance
                    $providerInstance = new $providerClass($providerModel,$providerApi,true);
                }else{
                    // Activate And Get Provider Instance
                    $providerInstance = new $providerClass($providerModel,$providerApi);
                }
                throw_unless($providerInstance instanceof PaymentProviderContract,$providerClass.' must implement App\Services\PaymentService\Contracts\PaymentProviderContract');
                // Add Provider Service In Providers List
                $this->providers[$provider] = $providerInstance;
                // Set Default Primary Provider
                if ($providerModel->is_primary && is_null($this->provider))
                {
                    $this->provider = $providerInstance;
                }
            }

        }

    }


    /**
     * Get Authentication For Payment Providers
     * @param string $provider
     * @return RazorpayApi|StripeApi|null
     */
    protected  function getProviderApi(string $provider): RazorpayApi|StripeApi|null
    {
        return match ($provider) {
            PaymentProviderModel::RAZORPAY => new RazorpayApi(config('services.razorpay.api_key'), config('services.razorpay.api_secret')),
            PaymentProviderModel::STRIPE => new StripeApi(config('services.stripe.sk_api_key')),
            default => null,
        };
    }


    /**
     * @param string $error
     * @return void
     */
    public function setError(string $error): void
    {
        $this->provider->setError($error);
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->provider->getError();
    }
}
