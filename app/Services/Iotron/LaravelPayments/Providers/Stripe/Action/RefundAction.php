<?php

namespace App\Services\Iotron\LaravelPayments\Providers\Stripe\Action;

use App\Services\Iotron\LaravelPayments\Contracts\Models\PaymentModelContract;
use App\Services\Iotron\LaravelPayments\Providers\Stripe\Stripe;

class RefundAction
{
    protected Stripe $provider;

    protected ?PaymentModelContract $payment = null;

    public function __construct(Stripe $provider, ?PaymentModelContract $payment)
    {
        $this->provider = $provider;
        $this->payment = $payment;
    }

    public function payment(PaymentModelContract $payment): static
    {
        $this->payment = $payment;

        return $this;
    }

    public function create()
    {

    }
}
