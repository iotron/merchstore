<?php

namespace App\Services\Iotron\LaravelPayments\Providers\Razorpay\Action;


use App\Models\Payment\Payment;
use App\Services\Iotron\LaravelPayments\Contracts\Models\PaymentModelContract;
use App\Services\Iotron\LaravelPayments\Providers\Razorpay\Razorpay;
use Throwable;

class VerifyAction
{
    protected Razorpay $provider;

    protected ?PaymentModelContract $payment = null;

    public function __construct(Razorpay $provider, ?PaymentModelContract $payment)
    {
        $this->provider = $provider;
        $this->payment = $payment;
    }

    public function payment(PaymentModelContract $payment): static
    {
        $this->payment = $payment;

        return $this;
    }

    public function request(array $data): bool
    {
        $success = false;
        $error = null;

        $attributes = [
            'razorpay_order_id' => $data['razorpay_order_id'],
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_signature' => $data['razorpay_signature'],
        ];

        try {
            if ($this->payment->provider_gen_id === $attributes['razorpay_order_id']) {
                $verify = $this->provider->getApi()->utility->verifyPaymentSignature($attributes);

                if (is_null($verify)) {
                    $success = true;
                }
            }

        } catch (Throwable $e) {
            report($e);
            $this->provider->setError($e->getMessage());
        }

        if ($success && is_null($this->provider->getError())) {
            $this->payment->fill([
                'provider_transaction_id' => $attributes['razorpay_payment_id'],
                'provider_gen_sign' => $attributes['razorpay_signature'],
                'status' => Payment::COMPLETED,
                'verified' => true,
            ])->save();

            return true;
        } else {
            return false;
        }
    }

    public function webhook(string $signature, object|string $response): bool
    {
        $success = true;
        try {
            $this->provider->getApi()->utility->verifyWebhookSignature($response, $signature, $this->provider->getWebhookSecret());
        } catch (Throwable $e) {
            report($e);
            $success = false;
            $this->provider->setError($e->getMessage());
        }

        return $success && empty($this->provider->getError());
    }

    /**
     * @throws Throwable
     */
    public function webhookCustom(array $data): bool
    {
        $received_signature = $data['signature'] ?? null;
        $body = $data['body'] ?? null;
        if (is_null($body)) {
            $body = $data['content'] ?? null;
        }
        throw_if(is_null($body), 'razorpay webhook request body is missing');
        throw_if(is_null($received_signature), 'razorpay webhook validation signature is missing');
        $expected_signature = hash_hmac('sha256', $body, $this->provider->getWebhookSecret());
        throw_if(($expected_signature != $received_signature), 'RazorpayPayment Signature Not Matched!');

        return true;
    }
}
