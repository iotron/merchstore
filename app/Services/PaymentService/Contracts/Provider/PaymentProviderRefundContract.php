<?php

namespace App\Services\PaymentService\Contracts\Provider;

use App\Services\Iotron\Money\Money;

interface PaymentProviderRefundContract
{
    public function create(int|string $payment_id, int|string|Money $amount);
}
