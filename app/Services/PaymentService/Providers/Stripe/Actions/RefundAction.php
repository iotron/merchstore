<?php

namespace App\Services\PaymentService\Providers\Stripe\Actions;

use App\Models\Customer\Booking;
use App\Models\Customer\Payment;
use App\Models\Customer\Refund;
use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderRefundContract;
use App\Services\PaymentService\Providers\Stripe\StripePaymentServiceContract;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class RefundAction implements PaymentProviderRefundContract
{

    protected StripeClient $api;
    protected PaymentProviderContract|StripePaymentServiceContract $provider;


    public function __construct(StripeClient $api_key,PaymentProviderContract $provider)
    {
        $this->api = $api_key;
        $this->provider = $provider;


    }


    /**
     * @param Booking $booking
     * @return mixed
     * @throws ApiErrorException
     */
    public function create(Booking $booking):Refund
    {
        $payment = $booking->payment;

        if ($payment->details['metadata']['provider_mode'] == 'checkout')
        {
            $paymentIntentObject = $this->api->paymentIntents->retrieve($payment->provider_ref_id);
        }else{
            $paymentIntentObject = $this->api->paymentIntents->retrieve($payment->provider_gen_id);
        }
        //Get Payment Intent Charge Instance
        $chargeInstance = $this->api->charges->retrieve($paymentIntentObject->latest_charge);

        if (!$chargeInstance->refunded)
        {
            $refundInstance = $this->api->refunds->create([
                'charge' => $chargeInstance->id,
            ]);
            if ($refundInstance->status == 'succeeded')
            {
                $payment->status = Payment::REFUND;
                $payment->save();
                $booking->refund()->create([
                    'refund_id' => $refundInstance->id,
                    'amount' => $booking->total->getAmount(),
                    'currency' =>$booking->total->getCurrency()->getCurrency(),
                    'payment_id'=> $payment->id,
                    'receipt' => $booking->payment->receipt,
                    'speed' => null,
                    'status' => Refund::COMPLETED,
                    'batch_id' => null,
                    'notes' => $refundInstance->toArray(),
                    'tracking_data' => [],
                    'details' => $refundInstance->toArray(),
                    'error' => $this->provider->getError(),
                ]);
            }else{
                $payment->status = Payment::CANCEL_REFUND;
                $payment->save();
            }
        }
        return $booking->refund;

    }


}
