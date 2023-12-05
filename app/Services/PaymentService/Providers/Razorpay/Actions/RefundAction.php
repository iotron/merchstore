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
     * @param int|string $payment_id
     * @param int|string|Money $amount
     * @return mixed|void
     */
    public function create(int|string $payment_id, int|string|Money $amount)
    {

        try {

            $response = $this->api->payment->fetch($payment_id)->refund([
                "amount"=> ($amount instanceof Money) ? $amount->getAmount() : $amount,
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
