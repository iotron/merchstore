<?php

namespace App\Services\PaymentService\Providers\CashOnDelivery;

use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderMethodContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderPayoutContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderRefundContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderVerificationContract;

class CodPaymentService implements PaymentProviderContract
{

    //

    public function __construct()
    {
    }

    public function getApi(): object
    {
        return $this;
    }

    public function getProviderName(): string
    {
        return 'cod';
    }

    public function getClass(): string
    {
        return get_class($this);
    }

    public function setError(string $error): void
    {
        // TODO: Implement setError() method.
    }

    public function getError(): ?string
    {
        // TODO: Implement getError() method.
    }

    public function order(): PaymentProviderMethodContract
    {
        // TODO: Implement order() method.
    }

    public function payment(): PaymentProviderMethodContract
    {
        // TODO: Implement payment() method.
    }

    public function verify(): PaymentProviderVerificationContract
    {
        // TODO: Implement verify() method.
    }

    public function refund(): PaymentProviderRefundContract
    {
        // TODO: Implement refund() method.
    }

    public function payout(): PaymentProviderPayoutContract
    {
        // TODO: Implement payout() method.
    }

}
