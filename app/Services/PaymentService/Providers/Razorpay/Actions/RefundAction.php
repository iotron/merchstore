<?php

namespace App\Services\PaymentService\Providers\Razorpay\Actions;

use App\Helpers\Money\Money;

use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\Models\Payment\Refund;
use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderRefundContract;
use App\Services\PaymentService\Providers\Razorpay\RazorpayApi;
use App\Services\PaymentService\Providers\Razorpay\RazorpayPaymentServiceContract;
use Razorpay\Api\Api;

class RefundAction implements PaymentProviderRefundContract
{

    protected RazorpayApi $api;
    protected PaymentProviderContract|RazorpayPaymentServiceContract $paymentProvider;

    public function __construct(RazorpayApi $api, PaymentProviderContract|RazorpayPaymentServiceContract $paymentProvider)
    {
        $this->api = $api;
        $this->paymentProvider = $paymentProvider;
    }

    /**
     * @param Payment $payment
     * @return mixed|void
     */
    public function create(Payment $payment)
    {

        try {


            $response = $this->api->payment->fetch($payment->provider_ref_id)->refund([
                "amount"=> ($payment->total instanceof Money) ? $payment->total->getAmount() : $payment->total,
                "speed"=> $this->paymentProvider->getSpeed(),
            ]);

           return $response;


        }catch (\Throwable $e)
        {
            report($e);
            $this->paymentProvider->setError($e->getMessage());
        }


    }
}
