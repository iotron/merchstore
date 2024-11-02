<?php

namespace App\Services\Iotron\LaravelPayments\Providers\Cash\Action;

use App\Services\Iotron\LaravelPayments\Providers\Cash\Cash;
use Illuminate\Support\Str;

class OrderAction
{
    protected Cash $provider;

    protected array $data;

    public function __construct(Cash $provider, array $data = [])
    {
        $this->provider = $provider;
        $this->data = $data;
    }

    public function create(array $data = [])
    {
        $providerData = empty($data) ? $this->data['provider'] : $data;
        $response = [
            'id' => 'cash_order_'.Str::random(10),
            'status' => 'created',
            'entity' => 'order',
            'amount' => $providerData['amount'],
            'amount_paid' => 0,
            'amount_due' => $providerData['amount'],
            'created' => now()->format('dmYHis'),
        ];
        $additionalData = empty($data) ? $this->data['additional'] : $data;

        return [

            'provider_gen_id' => $response['id'],
            'provider_transaction_id' => null,
            'provider_generated_sign' => null,
            'amount' => $response['amount'] ?? $providerData['amount'] ?? 0,
            'callback_url' => $additionalData['callback_url'] ?? '',
            'success_url' => $additionalData['success_url'] ?? '',
            'failure_url' => $additionalData['failure_url'] ?? '',
            'payment_provider_id' => $this->provider->getModel()->id,
            'details' => [
                'provider' => $response,
                'additional' => $additionalData,
            ],
        ];
    }
}
