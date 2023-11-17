<?php

namespace App\Services\PaymentService\Providers\Stripe\Actions;

use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderMethodContract;
use App\Services\PaymentService\Providers\Stripe\StripePaymentServiceContract;
use Stripe\StripeClient;

class PaymentAction implements PaymentProviderMethodContract
{

    protected StripeClient $api;
    protected PaymentProviderContract|StripePaymentServiceContract $paymentProvider;


    public function __construct(StripeClient $api_key,PaymentProviderContract $paymentProvider)
    {
        $this->api = $api_key;
        $this->paymentProvider = $paymentProvider;


    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    /**
     * @param string|int $id
     * @return mixed
     */
    public function fetch(int|string $id)
    {
        // TODO: Implement fetch() method.
    }

    /**
     * @return mixed
     */
    public function all()
    {
        // TODO: Implement all() method.
    }


}
