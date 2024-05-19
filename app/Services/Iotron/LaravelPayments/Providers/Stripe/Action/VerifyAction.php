<?php

namespace App\Services\Iotron\LaravelPayments\Providers\Stripe\Action;


use App\Models\Payment\Payment;
use App\Services\Iotron\LaravelPayments\Contracts\Models\PaymentModelContract;
use App\Services\Iotron\LaravelPayments\Providers\Stripe\Stripe;

class VerifyAction
{
    protected Stripe $provider;

    protected ?PaymentModelContract $payment = null;

    public function __construct(Stripe $provider, ?PaymentModelContract $payment)
    {
        $this->provider = $provider;
        $this->payment = $payment;
    }

    public function payment(PaymentModelContract $payment): static
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * @throws \Throwable
     */
    public function request(array $data): bool
    {
        $isPaid = false;
        throw_unless($data['session_id'], 'Session ID is required');
        throw_unless($this->payment, 'Payment is required');
        $matched = $this->payment->provider_order_id == $data['session_id'];
        $paymentIntentId = null;
        // Fetch Checkout Object
        $stripeCheckout = $this->provider->getApi()->checkout->sessions->retrieve($data['session_id']);
        if ($stripeCheckout->payment_status == 'paid' && $stripeCheckout->status == 'complete') {
            $paymentIntentId = $stripeCheckout->payment_intent;
            // Fetch Payment Intent With PaymentIntentId
            $paymentIntent = $this->provider->getApi()->paymentIntents->retrieve($paymentIntentId);
            if ($paymentIntent->status == 'succeeded' && $paymentIntent->amount_received) {
                $isPaid = true;
            }
        }

        if ($isPaid) {
            $this->payment->fill([
                'provider_transaction_id' => $paymentIntentId,
                'provider_gen_sign' => $this->payment->details['provider']['client_secret'],
                'status' => Payment::COMPLETED,
                'verified' => true,
            ])->save();
        }

        return $isPaid;

    }
}
