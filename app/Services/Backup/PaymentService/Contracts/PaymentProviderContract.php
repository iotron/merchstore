<?php

namespace App\Services\PaymentService\Contracts;

use App\Models\Payment\PaymentProvider;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderMethodContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderOrderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderPayoutContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderRefundContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderVerificationContract;

interface PaymentProviderContract
{
    public function getApi(): object;

    public function getProviderName(): string;

    public function getClass(): string;

    public function getModel(): ?PaymentProvider;

    public function getProvider(): static|PaymentProviderContract;

    public function setError(string $error): void;

    public function getError(): ?string;

    public function order(): PaymentProviderOrderContract;

    public function payment(): PaymentProviderMethodContract;

    public function verify(): PaymentProviderVerificationContract;

    public function refund(): PaymentProviderRefundContract;

    public function payout(): PaymentProviderPayoutContract;
}
