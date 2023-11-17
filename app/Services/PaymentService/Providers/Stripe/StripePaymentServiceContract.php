<?php

namespace App\Services\PaymentService\Providers\Stripe;

interface StripePaymentServiceContract
{

    public function hasIntent(): static;

    public function hasCheckout(): static;
    public function isIntent(): bool;

    public function isCheckout(): bool;

    public function isCard(): bool;

    public function isWallet(): bool;

    public function getIntentType(): string;

    public function getWalletType(): ?string;

    public function isSubscribable(): bool;

    public function canTakeTransactionCharge(): bool;

    public function getTransactionFee(): int;

    public function displayTerms():bool;

}
