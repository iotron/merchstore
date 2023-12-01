<?php

namespace App\Services\PaymentService\Contracts\Provider;


use App\Models\Order\Order;
use App\Models\Payment\Payment;

interface PaymentProviderRefundContract
{


    public function create(Payment $payment);

}
