<?php

namespace App\Services\Iotron\LaravelRazorpay\Support\Traits;

/**
 * Trait HasLaravelRazorpayPaymentProvider
 *
 * Provides methods for accessing and decoding Razorpay payment provider credentials.
 */
trait HasLaravelRazorpayPaymentProvider
{
    /**
     * Get the Razorpay key, decoding it from base64.
     *
     * @return string The decoded Razorpay key.
     */
    public function getRazorpayKey(): string
    {
        return base64_decode($this->key,true);
    }

    /**
     * Get the Razorpay secret, decoding it from base64.
     *
     * @return string The decoded Razorpay secret.
     */
    public function getRazorpaySecret(): string
    {
        return base64_decode($this->secret,true);
    }

    /**
     * Get the Razorpay webhook URL, decoding it from base64.
     *
     * @return string The decoded Razorpay webhook URL.
     */
    public function getRazorpayWebhook(): string
    {
        return base64_decode($this->webhook,true);
    }
}
