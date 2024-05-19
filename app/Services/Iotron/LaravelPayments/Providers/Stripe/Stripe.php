<?php

namespace App\Services\Iotron\LaravelPayments\Providers\Stripe;

use App\Models\Customer\PaymentProvider;
use App\Services\Iotron\LaravelPayments\Contracts\Builder\OrderBuilderContract;
use App\Services\Iotron\LaravelPayments\Contracts\Builder\OrderMakerContract;
use App\Services\Iotron\LaravelPayments\Contracts\Models\PaymentModelContract;
use App\Services\Iotron\LaravelPayments\Contracts\Models\PaymentProviderModelContract;
use App\Services\Iotron\LaravelPayments\Contracts\Providers\LaravelPaymentProviderContract;
use App\Services\Iotron\LaravelPayments\Providers;
use App\Services\Iotron\LaravelPayments\Providers\Stripe\Builder\OrderBuilder;
use Illuminate\Database\Eloquent\Model;
use Stripe\StripeClient;

class Stripe implements LaravelPaymentProviderContract
{
    protected PaymentProviderModelContract|PaymentProvider $model;

    protected StripeClient $api;

    private OrderBuilderContract $orderBuilder;

    public function __construct(PaymentProviderModelContract $paymentProvider, array $auth)
    {
        $this->model = $paymentProvider;
        $this->api = new StripeClient($auth['secret']);
        $this->orderBuilder = new OrderBuilder();
    }

    public function get(): static
    {
        return $this;
    }

    public function getApi(): StripeClient
    {
        return $this->api;
    }

    public function getModel(): PaymentProvider|Model
    {
        return $this->model;
    }

    public function order(?OrderMakerContract $orderMaker): Action\OrderAction
    {
        return new Providers\Stripe\Action\OrderAction($this, $orderMaker->builder($this->orderBuilder)->toArray());
    }

    public function verify(?PaymentModelContract $payment_model = null): Action\VerifyAction
    {
        return new Providers\Stripe\Action\VerifyAction($this, $payment_model);
    }

    public function refund(?PaymentModelContract $payment_model = null): Action\RefundAction
    {
        return new Providers\Stripe\Action\RefundAction($this, $payment_model);
    }
}
