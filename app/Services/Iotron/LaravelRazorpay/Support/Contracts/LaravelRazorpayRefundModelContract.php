<?php

namespace App\Services\Iotron\LaravelRazorpay\Support\Contracts;

use App\Services\Iotron\LaravelRazorpay\Support\Cast\RefundModelStatusCast;

interface LaravelRazorpayRefundModelContract
{


    public function getRazorpayRefundId():string;


    public function getStatus():RefundModelStatusCast|string;

}
