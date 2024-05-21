<?php

namespace App\Services\PaymentService\Contracts\Provider;

use App\Helpers\Money\Money;

interface PaymentProviderRefundContract
{
    public function create(int|string $payment_id, int|string|Money $amount);
}
