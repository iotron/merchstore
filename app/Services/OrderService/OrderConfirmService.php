<?php

namespace App\Services\OrderService;

use App\Models\Payment\Payment;

class OrderConfirmService
{


    protected Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function updateOrder()
    {
    }

    public function getOrder()
    {
        return $this->payment->order;
    }
}
