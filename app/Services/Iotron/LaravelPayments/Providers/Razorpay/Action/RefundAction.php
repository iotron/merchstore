<?php

namespace App\Services\Iotron\LaravelPayments\Providers\Razorpay\Action;

use App\Services\Iotron\LaravelPayments\Contracts\Models\PaymentModelContract;
use App\Services\Iotron\LaravelPayments\Providers\Razorpay\Razorpay;

class RefundAction
{
    protected Razorpay $provider;

    protected ?PaymentModelContract $payment = null;

    public function __construct(Razorpay $provider, ?PaymentModelContract $payment)
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
        $response = $this->provider->getApi()->payment->fetch($this->payment->getTransactionId())->refund([
            'amount' => $this->payment->amount,
            'speed' => config('laravel-payments.config.razorpay.speed'),
        ]);

        $responseData = $response->toArray();

        return [
            'id' => $responseData['id'],
            'provider_transaction_id' => $responseData['payment_id'],
            'status' => $responseData['status'],
            'amount' => $responseData['amount'],
            'details' => $responseData,
        ];
    }
}
