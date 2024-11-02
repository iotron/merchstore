<?php

namespace App\Services\Iotron\LaravelRazorpay\Support\Contracts;

/**
 * Interface LaravelRazorpayPaymentProviderModelContract
 *
 * Defines the contract for payment provider model classes used with Razorpay.
 */
interface LaravelRazorpayPaymentProviderModelContract
{
    /**
     * Get the Razorpay API key for the payment provider.
     *
     * @return string The Razorpay API key.
     */
    public function getRazorpayKey(): string;

    /**
     * Get the Razorpay API secret for the payment provider.
     *
     * @return string The Razorpay API secret.
     */
    public function getRazorpaySecret(): string;

    /**
     * Get the Razorpay webhook secret for the payment provider.
     *
     * @return string The Razorpay webhook secret.
     */
    public function getRazorpayWebhook(): string;
}
