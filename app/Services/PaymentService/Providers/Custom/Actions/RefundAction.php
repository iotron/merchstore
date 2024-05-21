<?php

namespace App\Services\PaymentService\Providers\Custom\Actions;

use App\Helpers\Money\Money;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderRefundContract;
use Illuminate\Support\Str;

class RefundAction implements PaymentProviderRefundContract
{
    /**
     * @return mixed
     */
    public function create(int|string $payment_id, int|string|Money $amount)
    {
        return [
            'id' => Str::random(6),
            'amount' => $amount instanceof Money ? $amount->getAmount() : $amount,
            'currency' => $amount instanceof Money ? $amount->getCurrency()->getCurrency() : config('config.services.defaults.currency'),
            'payment_id' => $payment_id,
            'speed_processed' => 'custom',
            'batch_id' => Str::uuid()->toString(),
            'notes' => [],
            'acquirer_data' => [],
            'error' => null,
            'status' => 'processed',
        ];

    }

    protected function getErrorMsg()
    {
        return [
            'error' => [
                'code' => 'PAYMENT_NOT_FOUND_ERROR',
                'description' => 'No Payment ID Found & Payment Not Verified',
                'step' => 'payment_initiation',
                'reason' => 'input_validation_failed',
            ],
        ];
    }
}
