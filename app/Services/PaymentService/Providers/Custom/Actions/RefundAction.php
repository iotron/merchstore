<?php

namespace App\Services\PaymentService\Providers\Custom\Actions;

use App\Models\Payment\Payment;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderRefundContract;

class RefundAction implements PaymentProviderRefundContract
{

    /**
     * @param Payment $payment
     * @return mixed
     */
    public function create(Payment $payment)
    {
        dd('custom refund');
    }
}
