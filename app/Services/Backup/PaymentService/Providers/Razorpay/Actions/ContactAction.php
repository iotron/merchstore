<?php

namespace App\Services\PaymentService\Providers\Razorpay\Actions;

use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Providers\Razorpay\RazorpayApi;
use App\Services\PaymentService\Providers\Razorpay\RazorpayPaymentServiceContract;

class ContactAction
{
    protected RazorpayApi $api;

    protected PaymentProviderContract|RazorpayPaymentServiceContract $paymentProvider;

    public function __construct(RazorpayApi $api, PaymentProviderContract $paymentProvider)
    {
        $this->api = $api;
        $this->paymentProvider = $paymentProvider;
    }
}
