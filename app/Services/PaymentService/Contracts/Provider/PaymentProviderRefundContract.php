<?php

namespace App\Services\PaymentService\Contracts\Provider;


use App\Helpers\Money\Money;
use App\Models\Order\Order;
use App\Models\Payment\Payment;

interface PaymentProviderRefundContract
{


    public function create(int|string $payment_id, int|string|Money $amount);

}
