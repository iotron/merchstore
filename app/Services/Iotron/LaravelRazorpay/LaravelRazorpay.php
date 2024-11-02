<?php

namespace App\Services\Iotron\LaravelRazorpay;

use App\Services\Iotron\LaravelRazorpay\Support\Contracts\LaravelRazorpayPaymentProviderContract;
use App\Services\Iotron\LaravelRazorpay\Support\Contracts\LaravelRazorpayPaymentProviderModelContract;
use Illuminate\Database\Eloquent\Model;
use Razorpay\Api\Api;

/**
 * Class LaravelRazorpay
 *
 * This class handles the integration with Razorpay.
 */
class LaravelRazorpay implements LaravelRazorpayPaymentProviderContract
{
    public const RAZORPAY = 'Razorpay';

    private ?string $key = null;
    private ?string $secret = null;
    private ?string $webhook = null;
    private null|string $paymentProviderModelClass = null;
    private null|string $paymentModelClass = null;
    protected Api $api;
    protected null|LaravelRazorpayPaymentProviderModelContract $paymentProviderRecord = null;
    protected ?string $error = null;

    /**
     * LaravelRazorpay constructor.
     * Initializes the configuration and sets up the Razorpay API instance.
     */
    public function __construct()
    {
        $this->resolveConfiguration();
        $this->api = new Api($this->key, $this->secret);
    }

    /**
     * Resolve the configuration settings for Razorpay.
     *
     * This method fetches the configuration from the config file and sets the
     * necessary properties. If the payment provider status is enabled, it loads
     * the authentication configuration from the model.
     */
    private function resolveConfiguration(): void
    {
        if (config('laravel-razorpay.payment-provider.status')) {
            $this->paymentProviderModelClass = config('laravel-razorpay.payment-provider.model.class');
            $urlValue = config('laravel-razorpay.payment-provider.url');
            $this->paymentProviderRecord = $this->paymentProviderModelClass::firstWhere('url', $urlValue);
        }

        $this->key = $this->paymentProviderRecord?->getRazorpayKey() ?? config('laravel-razorpay.auth.key');
        $this->secret = $this->paymentProviderRecord?->getRazorpaySecret() ?? config('laravel-razorpay.auth.secret');
        $this->webhook = $this->paymentProviderRecord?->getRazorpayWebhook() ?? config('laravel-razorpay.auth.webhook_secret');
        $this->paymentModelClass = config('laravel-razorpay.payment.model.class');
    }

    /**
     * Create a new instance of the LaravelRazorpay service.
     *
     * @return LaravelRazorpayPaymentProviderContract
     */
    public static function make(): LaravelRazorpayPaymentProviderContract
    {
        return app(LaravelRazorpay::class)->getInstance();
    }

    /**
     * Get the current instance of the LaravelRazorpay service.
     *
     * @return LaravelRazorpayPaymentProviderContract
     */
    protected function getInstance(): LaravelRazorpayPaymentProviderContract
    {
        return $this;
    }

    /**
     * Get the Razorpay API instance.
     *
     * @return Api
     */
    public function getApi(): Api
    {
        return $this->api;
    }

    /**
     * Get the payment provider model.
     *
     * @return Model|null
     */
    public function getModel(): ?Model
    {
        return $this->paymentProviderRecord;
    }

    /**
     * Check if a payment provider model is set.
     *
     * @return bool
     */
    public function hasModel(): bool
    {
        return !is_null($this->paymentProviderRecord);
    }

    /**
     * Get the class of the payment provider model.
     *
     * @return string|null
     */
    public function getModelClass(): ?string
    {
        return $this->paymentProviderModelClass;
    }

    /**
     * Set the class of the payment model.
     *
     * @param Model|string $model
     * @return static
     */
    public function setPaymentModelClass(Model|string $model): static
    {
        $this->paymentModelClass = ($model instanceof Model) ? get_class($model) : $model;
        return $this;
    }

    /**
     * Get the class of the payment model.
     *
     * @return string|null
     */
    public function getPaymentModelClass(): ?string
    {
        return $this->paymentModelClass;
    }

    /**
     * Set an error message.
     *
     * @param string $error_text
     */
    public function setError(string $error_text): void
    {
        $this->error = $error_text;
    }

    /**
     * Get the error message.
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Get the speed configuration for refunds.
     *
     * @return string
     */
    public function getSpeed(): string
    {
        return config('laravel-razorpay.speed');
    }

    /**
     * Get the Razorpay webhook secret.
     *
     * @return string
     */
    public function getWebhookSecret(): string
    {
        return $this->webhook;
    }

    // Razorpay Payment Provider Actions

    /**
     * Get the OrderAction instance.
     *
     * @return Actions\OrderAction
     */
    public function order(): Actions\OrderAction
    {
        return new Actions\OrderAction($this);
    }

    /**
     * Get the VerifyAction instance.
     *
     * @return Actions\VerifyAction
     */
    public function verify(): Actions\VerifyAction
    {
        return new Actions\VerifyAction($this);
    }

    /**
     * Get the RefundAction instance.
     *
     * @return Actions\RefundAction
     */
    public function refund(): Actions\RefundAction
    {
        return new Actions\RefundAction($this);
    }

    /**
     * Get the QRCodeAction instance.
     *
     * @return Actions\QRCodeAction
     */
    public function qr(): Actions\QRCodeAction
    {
        return new Actions\QRCodeAction($this);
    }

    /**
     * Get the PaymentLinkAction instance.
     *
     * @return Actions\PaymentLinkAction
     */
    public function link(): Actions\PaymentLinkAction
    {
        return new Actions\PaymentLinkAction($this);
    }

    /**
     * Get the CustomerAction instance.
     *
     * @return Actions\CustomerAction
     */
    public function razorpayCustomer(): Actions\CustomerAction
    {
        return new Actions\CustomerAction($this);
    }
}
