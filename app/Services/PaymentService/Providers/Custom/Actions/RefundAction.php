<?php

namespace App\Services\PaymentService\Providers\Custom\Actions;

use App\Models\Payment\Payment;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderRefundContract;
use Illuminate\Support\Str;

class RefundAction implements PaymentProviderRefundContract
{

    /**
     * @param Payment $payment
     * @return mixed
     */
    public function create(Payment $payment)
    {
       if (is_null($payment->provider_ref_id) && !$payment->verified)
       {
           return [
               'error' => [
                   'code'        => 'PAYMENT_NOT_FOUND_ERROR',
                   'description' => 'No Payment ID Found & Payment Not Verified',
                   "step"        => "payment_initiation",
                   "reason"      => "input_validation_failed",
               ]
           ];

       }else{
           return [
               'id' => Str::random(6),
               'amount' => $payment->total->getAmount(),
               'currency' => $payment->total->getCurrency()->getCurrency(),
               'payment_id' => $payment->provider_ref_id,
               'speed_processed' => 'custom',
               'batch_id'   => Str::uuid()->toString(),
               'notes' => [],
               'acquirer_data' => [],
               'error' => null,
               'status' => 'processed'
           ];
       }

    }
}
