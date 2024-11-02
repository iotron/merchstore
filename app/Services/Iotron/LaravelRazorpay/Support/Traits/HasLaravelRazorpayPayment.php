<?php

namespace App\Services\Iotron\LaravelRazorpay\Support\Traits;

use App\Services\Iotron\LaravelRazorpay\Support\Cast\PaymentModelTypeCast;

/**
 * Trait HasLaravelRazorpay
 *
 * Provides common methods for accessing Razorpay payment model attributes.
 */
trait HasLaravelRazorpayPayment
{
    /**
     * Get the Razorpay generated ID for the payment model.
     *
     * @return string|null The Razorpay generated ID or null if not set.
     */
    public function getRazorpayGeneratedId(): ?string
    {
        return $this->provider_gen_id;
    }

    /**
     * Get the Razorpay transaction ID for the payment model.
     *
     * @return string|null The Razorpay transaction ID or null if not set.
     */
    public function getRazorpayTransactionId(): ?string
    {
        return $this->provider_transaction_id;
    }

    /**
     * Get the Razorpay generated signature for the payment model.
     *
     * @return string|null The Razorpay generated signature or null if not set.
     */
    public function getRazorpayGeneratedSign(): ?string
    {
        return $this->provider_generated_sign;
    }

    /**
     * Get the type of the payment model.
     *
     * @return string|PaymentModelTypeCast  The type of the payment model.
     */
    public function getType(): string|PaymentModelTypeCast
    {
        return $this->type;
    }

    /**
     * Check if the payment model status is marked as paid.
     *
     * @return bool True if the status is completed, false otherwise.
     */
    public function isPaid(): bool
    {
        return (bool) $this->verified;
    }
}
