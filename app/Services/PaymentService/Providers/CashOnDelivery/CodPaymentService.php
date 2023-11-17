<?php

namespace App\Services\PaymentService\Providers\CashOnDelivery;

use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderMethodContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderPayoutContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderRefundContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderVerificationContract;
use App\Services\PaymentService\Providers\CashOnDelivery\Actions\OrderAction;


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

    public function order(): PaymentProviderMethodContract
    {
        return new OrderAction($this);
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
