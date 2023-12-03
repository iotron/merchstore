<?php

namespace App\Services\PaymentService\Providers\Razorpay\Actions;

use App\Models\Payment\Payment;
use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderVerificationContract;
use App\Services\PaymentService\Providers\Razorpay\RazorpayApi;
use App\Services\PaymentService\Providers\Razorpay\RazorpayPaymentServiceContract;
use Razorpay\Api\Api;
use Throwable;

class VerifyAction implements PaymentProviderVerificationContract
{


    protected RazorpayApi $api;
    protected PaymentProviderContract|RazorpayPaymentServiceContract $paymentProvider;


    public function __construct(RazorpayApi $api_key,PaymentProviderContract $paymentProvider)
    {
        $this->api = $api_key;
        $this->paymentProvider = $paymentProvider;
    }



    /**
     * @param Payment $payment
     * @param array $data
     * @return bool
     */
    public function verifyWith(Payment $payment, array $data): bool
    {
        //dd($payment,$data);

        $success = false;
        try {
            if ($payment->provider_gen_id == $data['razorpay_order_id'])
            {
                $preparedData = [
                    "razorpay_order_id" => $data['razorpay_order_id'],
                    "razorpay_payment_id" => $data['razorpay_payment_id'],
                    "razorpay_signature" => $data['razorpay_signature']
                ];

                $verify = $this->api->utility->verifyPaymentSignature($preparedData);

                if (is_null($verify))
                {
                    $verify = $this->verifyManuallySignature($preparedData);
                }

                dd($verify);

                if ($verify)
                {
                    $success = true;
                }
            }

        } catch (Throwable $e) {
            report($e);
            $this->paymentProvider->setError($e->getMessage());
        }
        if ($success && empty($this->error)) {
            return true;
        } else {
            return false;
        }
    }


    function verifyManuallySignature($data): bool
    {
        $generatedSignature = hash_hmac('sha256', $data['razorpay_order_id'] . "|" . $data['razorpay_payment_id'], config('services.razorpay.api_secret'));

        if (hash_equals($generatedSignature, $data['razorpay_signature'])) {
            // Payment is successful
            return true;
        } else {
            // Payment failed
            return false;
        }
    }





    /**
     * @param string $signature
     * @param object|string $response
     * @return bool
     */
    public function webhook(string $signature, object|string $response): bool
    {
        $success = true;
        try {
            $this->api->utility->verifyWebhookSignature($response, $signature, $this->paymentProvider->getWebhookSecret());
        } catch (Throwable $e) {
            report($e);
            $success = false;
            $this->paymentProvider->setError($e->getMessage());
        }
        return ($success && empty($this->paymentProvider->getError()));
    }

    /**
     * @param array $data
     * @return bool
     */
    public function webhookCustom(array $data): bool
    {
        $received_signature = $data['signature'] ?? null;
        $body = $data['body'] ?? null;
        if (is_null($body))
        {
            $body = $data['content'] ?? null;
        }
        throw_if(is_null($body),'razorpay webhook request body is missing');
        throw_if(is_null($received_signature),'razorpay webhook validation signature is missing');
        $expected_signature = hash_hmac('sha256', $body, $this->paymentProvider->getWebhookSecret());
        throw_if(($expected_signature != $received_signature),'RazorpayPayment Signature Not Matched!');
        return true;
    }
}
