<?php

namespace App\Services\Iotron\LaravelPayments\Providers\Razorpay\Action;

use App\Services\Iotron\LaravelPayments\Providers\Razorpay\Razorpay;

class OrderAction
{
    protected Razorpay $provider;

    protected array $data;

    public function __construct(Razorpay $provider, array $data = [])
    {
        $this->provider = $provider;
        $this->data = $data;
    }

    public function create(array $data = [])
    {
        $providerData = empty($data) ? $this->data['provider'] : $data;
        $response = $this->provider->getApi()->order->create($providerData);
        $additionalData = empty($data) ? $this->data['additional'] : $data;

        $responseData = $response->toArray();

        return [
            'provider_gen_id' => $responseData['id'],
            'provider_transaction_id' => null,
            'provider_generated_sign' => null,
            'amount' => $responseData['amount'] ?? $providerData['amount'] ?? 0,
            'callback_url' => $additionalData['callback_url'] ?? '',
            'success_url' => $additionalData['success_url'] ?? '',
            'failure_url' => $additionalData['failure_url'] ?? '',
            'payment_provider_id' => $this->provider->getModel()->id,
            'details' => [
                'provider' => $responseData,
                'additional' => $additionalData,
            ],
        ];

    }
}
