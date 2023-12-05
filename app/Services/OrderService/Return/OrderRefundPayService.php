<?php

namespace App\Services\OrderService\Return;

use App\Models\Payment\Payment;
use App\Models\Payment\Refund;
use App\Services\PaymentService\Contracts\PaymentProviderContract;


class OrderRefundPayService
{

    protected ?string $error = null;
    protected PaymentProviderContract $paymentProvider;
    protected Refund $refund;
    protected Payment $payment;

    public function __construct(PaymentProviderContract $paymentProvider, Refund $refund)
    {

        $this->paymentProvider = $paymentProvider;
        $refund->loadMissing('payment');
        $this->refund = $refund;
        $this->payment = $this->refund->payment;

    }

    public function getError():?string
    {
        return $this->error;
    }


    public function refund()
    {
        $response =  $this->paymentProvider->refund()->create($this->payment->provider_ref_id,$this->refund->amount);

        if (isset($response['status']) && $response['status'] == 'processed')
        {

            if ($response['payment_id'] === $this->payment->provider_ref_id)
            {

                // Update Payment
//                $this->payment->fill([
//                    'status' => Payment::REFUND
//                ])->save();
                // Create And Return Refund Model
                 $this->refund->fill([
                    'refund_id' => $response['id'],
                    'amount' => $response['amount'],
                    'currency' => $response['currency'],
                    'provider_payment_id' => $response['payment_id'],
                    'speed' => $response['speed_processed'],
                    'status' => Refund::COMPLETED,
                    'batch_id' => $response['batch_id'],
                    'notes' => is_array($response['notes']) ? $response['notes'] : $response['notes']->toArray(),
                    'tracking_data' => is_array($response['acquirer_data']) ? $response['acquirer_data'] : $response['acquirer_data']->toArray(),
                    'details' => is_array($response) ? $response : $response->toArray(),
                    'error' => $response['error'] ?? null,
                ])->save();
            }else{
                $this->error = 'Payment Info Not Matched With Refund';
            }


        }else{
            $this->error = $response['error']['description'];
        }
        return is_null($this->error);
    }

}
