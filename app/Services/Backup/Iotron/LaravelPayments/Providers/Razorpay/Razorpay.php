<?php

namespace App\Services\Iotron\LaravelPayments\Providers\Razorpay;

use App\Models\Payment\PaymentProvider;
use App\Services\Iotron\LaravelPayments\Contracts\Builder\OrderBuilderContract;
use App\Services\Iotron\LaravelPayments\Contracts\Builder\OrderMakerContract;
use App\Services\Iotron\LaravelPayments\Contracts\Models\PaymentModelContract;
use App\Services\Iotron\LaravelPayments\Contracts\Models\PaymentProviderModelContract;
use App\Services\Iotron\LaravelPayments\Providers;
use App\Services\Iotron\LaravelPayments\Providers\Razorpay\Builder\OrderBuilder;
use Illuminate\Database\Eloquent\Model;
use Razorpay\Api\Api;

class Razorpay
{
    protected Model|PaymentProviderModelContract $model;

    protected Api $api;

    private OrderBuilderContract $orderBuilder;

    protected ?string $error = null;

    public function __construct(PaymentProviderModelContract $paymentProvider, array $auth)
    {
        $this->model = $paymentProvider;
        $this->api = new Api($auth['key'], $auth['secret']);
        $this->orderBuilder = new OrderBuilder();
    }

    public function getApi(): Api
    {
        return $this->api;
    }

    public function getModel(): Model|PaymentProvider
    {
        return $this->model;
    }

    public function get(): static
    {
        return $this;
    }

    public function setError(string $error_text): void
    {
        $this->error = $error_text;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getWebhookSecret(): string
    {
        return config('laravel-payments.providers.razorpay.webhook_secret');
    }

    public function order(?OrderMakerContract $orderMaker): Action\OrderAction
    {
        return new Providers\Razorpay\Action\OrderAction($this, $orderMaker->builder($this->orderBuilder)->toArray());
    }

    public function verify(?PaymentModelContract $payment_model = null): Action\VerifyAction
    {
        return new Providers\Razorpay\Action\VerifyAction($this, $payment_model);
    }

    public function refund(?PaymentModelContract $payment_model = null): Action\RefundAction
    {
        return new Providers\Razorpay\Action\RefundAction($this, $payment_model);
    }
}
