<?php

namespace App\Services\Iotron\LaravelRazorpay\Support\Contracts;

use App\Services\Iotron\LaravelRazorpay\Actions;
use App\Services\Iotron\LaravelRazorpay\Actions\PaymentLinkAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface LaravelRazorpayPaymentProviderContract
 *
 * Defines the contract for the LaravelRazorpay payment provider.
 */
interface LaravelRazorpayPaymentProviderContract
{
    /**
     * Get the Razorpay API instance.
     *
     * @return object
     */
    public function getApi(): object;

    /**
     * Get the payment provider model.
     *
     * @return Model|null
     */
    public function getModel(): ?Model;

    /**
     * Set an error message.
     *
     * @param string $error_text
     */
    public function setError(string $error_text): void;

    /**
     * Get the error message.
     *
     * @return string|null
     */
    public function getError(): ?string;

    /**
     * Get the speed configuration for refunds.
     *
     * @return string
     */
    public function getSpeed(): string;

    /**
     * Get the Razorpay webhook secret.
     *
     * @return string
     */
    public function getWebhookSecret(): string;

    /**
     * Get the OrderAction instance.
     *
     * @return Actions\OrderAction
     */
    public function order(): Actions\OrderAction;

    /**
     * Get the VerifyAction instance.
     *
     * @return Actions\VerifyAction
     */
    public function verify(): Actions\VerifyAction;

    /**
     * Get the RefundAction instance.
     *
     * @return Actions\RefundAction
     */
    public function refund(): Actions\RefundAction;

    /**
     * Get the QRCodeAction instance.
     *
     * @return Actions\QRCodeAction
     */
    public function qr(): Actions\QRCodeAction;

    /**
     * Get the PaymentLinkAction instance.
     *
     * @return Actions\PaymentLinkAction
     */
    public function link(): PaymentLinkAction;

    /**
     * Get the CustomerAction instance.
     *
     * @return Actions\CustomerAction
     */
    public function razorpayCustomer(): Actions\CustomerAction;

    /**
     * Set the class of the payment model.
     *
     * @param Model|string $model
     * @return static
     */
    public function setPaymentModelClass(Model|string $model): static;
}
