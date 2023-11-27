<?php

namespace App\Services\PaymentService\Providers\Stripe;

use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderMethodContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderOrderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderPayoutContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderRefundContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderVerificationContract;
use App\Services\PaymentService\Providers\Stripe\Actions\OrderAction;
use App\Services\PaymentService\Providers\Stripe\Actions\RefundAction;
use App\Services\PaymentService\Providers\Stripe\Actions\VerifyAction;
use Stripe\StripeClient;

class StripePaymentService implements PaymentProviderContract,StripePaymentServiceContract
{

    protected ?string $error = null;
    protected StripeClient $api;
    protected bool $intentMode=false;
    protected bool $checkoutSessionMode = false;
    protected string $intentModeType = 'card';
    protected bool $isWallet = false;
    protected ?string $wallet_type = null;

    private int $transactionFee = 0;

    private bool $takeTransactionFee = false;

    private bool $isSubscribable =false;
    protected bool $display_terms = false;


    public function __construct(StripeClient $api_key)
    {
        $this->api = $api_key;
        $this->discoverConfig();
    }

    protected function discoverConfig()
    {
        // Stripe Payment Mode (PaymentIntent as intent, CheckoutSession as checkout)
        $mode = config('payment-provider.providers.stripe.mode');
        throw_unless(in_array($mode,['intent','checkout']),'stripe payment provider must be ');
        // Setup Stripe
        $modeData = config('payment-provider.providers.stripe.mode_data')[$mode];
        $this->intentMode = $mode == 'intent';
        $this->checkoutSessionMode = $mode == 'checkout';
        // Setup Payment Intent
        if ($this->intentMode)
        {
            $this->intentModeType = $modeData['type'];
            if ($this->intentModeType == 'wallet')
            {
                $this->isWallet = true;
                $this->wallet_type = $modeData['wallet']['type'];

            }
        }

        $this->isSubscribable =  config('payment-provider.providers.stripe.subscription');
        $this->takeTransactionFee = config('payment-provider.providers.stripe.take_transaction_fee');
        $this->transactionFee = config('payment-provider.providers.stripe.fee_amount');
        $this->display_terms = config('payment-provider.providers.stripe.terms');

    }


    public function hasIntent(): static
    {
        $this->intentMode = true;
        $this->checkoutSessionMode = false;
        return $this;
    }

    public function hasCheckout(): static
    {
        $this->checkoutSessionMode = true;
        $this->intentMode = false;
        return $this;
    }

    /**
     * @return bool
     */
    public function displayTerms(): bool
    {
        return $this->display_terms;
    }


    public function isIntent(): bool
    {
        return $this->intentMode;
    }

    public function isCheckout(): bool
    {
        return $this->checkoutSessionMode;
    }

    public function isCard(): bool
    {
        return $this->intentModeType == 'card';
    }

    public function isWallet(): bool
    {
        return $this->intentModeType == 'wallet';
    }

    public function getIntentType(): string
    {
        return $this->intentModeType;
    }

    public function getWalletType(): ?string
    {
        return $this->wallet_type;
    }


    public function isSubscribable(): bool
    {
        return $this->isSubscribable;
    }

    public function canTakeTransactionCharge(): bool
    {
        return $this->takeTransactionFee;
    }

    public function getTransactionFee(): int
    {
        return $this->transactionFee;
    }





    /**
     * @return PaymentProviderOrderContract
     */
    public function order(): PaymentProviderOrderContract
    {
        return new OrderAction($this->api,$this);
    }

    /**
     * @return PaymentProviderMethodContract
     */
    public function payment(): PaymentProviderMethodContract
    {
        // TODO: Implement payment() method.
    }

    /**
     * @return PaymentProviderVerificationContract
     */
    public function verify(): PaymentProviderVerificationContract
    {
        return new VerifyAction($this->api,$this);
    }

    /**
     * @return PaymentProviderRefundContract
     */
    public function refund(): PaymentProviderRefundContract
    {
        return new RefundAction($this->api,$this);
    }

    /**
     * @return object
     */
    public function getApi(): object
    {
        return $this->api;
    }

    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return 'stripe';
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return get_class($this);
    }

    /**
     * @param string $error
     * @return void
     */
    public function setError(string $error): void
    {
        $this->error = $error;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @return PaymentProviderPayoutContract
     */
    public function payout(): PaymentProviderPayoutContract
    {
        throw_unless(0>1,'Wrong Payment Provider Selected For Payout');
    }
}
