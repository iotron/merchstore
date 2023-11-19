<?php

namespace App\Services\PaymentService\Providers\Stripe\Actions;

use App\Models\Payment\Payment;
use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderVerificationContract;
use App\Services\PaymentService\Providers\Stripe\StripePaymentServiceContract;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class VerifyAction implements PaymentProviderVerificationContract
{

    protected StripeClient $api;
    protected PaymentProviderContract|StripePaymentServiceContract $paymentProvider;



    public function __construct(StripeClient $api_key,PaymentProviderContract $paymentProvider)
    {
        $this->api = $api_key;
        $this->paymentProvider = $paymentProvider;


    }


    /**
     * @param Payment $payment
     * @param array $data
     * @return bool
     * @throws ApiErrorException
     */
    public function verifyWith(Payment $payment, array $data): bool
    {
        $validatePaymentStatus = false;
        $validateModelStatus = false;
        $isPaid = false;
        $paymentMode = $payment->details['metadata']['provider_mode'];
        $paymentIntentId = null;

        if ($paymentMode == 'checkout')
        {
            if ($payment->details['id'] == $data['session_id'] && $data['session_id'] == $payment->provider_gen_id)
            {
                $validateModelStatus = true;
                // Fetch Checkout Object
                $stripeCheckout = $this->api->checkout->sessions->retrieve($data['session_id']);
                if ($stripeCheckout->payment_status == 'paid' && $stripeCheckout->status == 'complete')
                {
                    $paymentIntentId = $stripeCheckout->payment_intent;
                }

            }
        }

        if (is_null($paymentIntentId) && $paymentMode == 'intent')
        {
            $paymentIntentId = $payment->provider_gen_id;
            // do payment intent related payment validation
        }

        // Fetch Payment Intent With PaymentIntentId
        $paymentIntent = $this->api->paymentIntents->retrieve($paymentIntentId);
        if ($paymentIntent->status == 'succeeded' && $paymentIntent->amount_received)
        {
            $isPaid = true;
            $validatePaymentStatus = true;
        }

        if ($validatePaymentStatus && $validateModelStatus && $isPaid && !is_null($paymentIntentId))
        {
            // Update Payment Model
            $payment->provider_ref_id = $paymentIntentId;
            $payment->status = Payment::COMPLETED;
            $payment->save();

            return true;
        }
        return false;



    }

    /**
     * @param string $signature
     * @param object|string $response
     * @return bool
     */
    public function webhook(string $signature, object|string $response): bool
    {
        throw_if((1 == true),'stripe webhook not setup');
    }

    /**
     * @param array $data
     * @return bool
     */
    public function webhookCustom(array $data): bool
    {
        throw_if((1 == true),'stripe webhook not setup');
    }
}
