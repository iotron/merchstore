<?php

namespace App\Services\PaymentService\Providers\Razorpay\Actions;

use App\Helpers\Money\Money;
use App\Models\Customer\Booking;
use App\Models\Customer\Payment;
use App\Models\Customer\Refund;
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
     * @param Booking $booking
     * @return Refund
     */
    public function create(Booking $booking):Refund
    {

        try {

            if ($booking->refund()->count())
            {
                return $booking->refund;
            }

            $response = $this->api->payment->fetch($booking->payment->provider_ref_id)->refund([
                "amount"=> ($booking->amount instanceof Money) ? $booking->amount->getAmount() : $booking->amount,
                "speed"=> $this->paymentProvider->getSpeed(),
            ]);

            if ($response['status'] == 'processed')
            {
                // Update Payment
                $paymentModel = $booking->payment;
                $paymentModel->status = Payment::REFUND;
                $paymentModel->save();
                // Create And Return Refund Model
                return $booking->refund()->create([
                    'refund_id' => $response['id'],
                    'amount' => $response['amount'],
                    'currency' => $response['currency'],
                    'payment_id' => $response['payment_id'],
                    'receipt' => $booking->payment->receipt,
                    'speed' => $response['speed_processed'],
                    'status' => Refund::COMPLETED,
                    'batch_id' => $response['batch_id'],
                    'notes' => $response['notes']->toArray(),
                    'tracking_data' =>  $response['acquirer_data']->toArray(),
                    'details' => $response->toArray(),
                    'error' => $response['error'] ?? null,
                ]);
            }


        }catch (\Throwable $e)
        {
            report($e);
            $this->paymentProvider->setError($e->getMessage());
        }


    }
}
