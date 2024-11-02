<?php

namespace App\Services\Iotron\LaravelRazorpay\Support\Traits;

use App\Services\Iotron\LaravelRazorpay\Support\Cast\RefundModelStatusCast;

trait HasLaravelRazorpayRefund
{

    public function getRazorpayRefundId():string
    {
        return $this->refund_id;
    }

    public function getStatus():RefundModelStatusCast|string
    {
        return $this->status;
    }

}
