<?php

namespace App\Services\Iotron\LaravelRazorpay\Support\Contracts;

use App\Services\Iotron\LaravelRazorpay\Support\Cast\PaymentModelTypeCast;

/**
 * Interface LaravelRazorpayPaymentModelContract
 *
 * Defines the contract for payment model classes used with Razorpay.
 */
interface LaravelRazorpayPaymentModelContract
{
    /**
     * Get the Razorpay generated ID for the payment.
     *
     * @return string|null The Razorpay generated ID or null if not set.
     */
    public function getRazorpayGeneratedId(): ?string;

    /**
     * Get the Razorpay transaction ID associated with the payment.
     *
     * @return string|null The Razorpay transaction ID or null if not set.
     */
    public function getRazorpayTransactionId(): ?string;

    /**
     * Get the Razorpay generated signature for the payment.
     *
     * @return string|null The Razorpay generated signature or null if not set.
     */
    public function getRazorpayGeneratedSign(): ?string;

    /**
     * Get the type of the payment.
     *
     * @return string|PaymentModelTypeCast The payment type.
     */
    public function getType(): string|PaymentModelTypeCast;

    /**
     * Check if the payment is marked as paid.
     *
     * @return bool True if the payment is paid, false otherwise.
     */
    public function isPaid(): bool;
}
