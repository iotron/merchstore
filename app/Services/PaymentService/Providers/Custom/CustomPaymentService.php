<?php

namespace App\Services\PaymentService\Providers\Custom;

use App\Models\Payment\PaymentProvider;
use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderMethodContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderOrderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderPayoutContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderRefundContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderVerificationContract;
use App\Services\PaymentService\Providers\Custom\Actions\OrderAction;
use App\Services\PaymentService\Providers\Custom\Actions\RefundAction;


class CustomPaymentService implements PaymentProviderContract
{

    protected ?PaymentProvider $providerModel = null;

    //

    public function __construct(?PaymentProvider $providerModel,mixed $api=null)
    {
        $this->providerModel = $providerModel;
    }

    public function getApi(): object
    {
        return $this;
    }

    public function getProviderName(): string
    {
        return 'custom';
    }

    public function getClass(): string
    {
        return get_class($this);
    }

    public function getModel():?PaymentProvider
    {
        return $this->providerModel;
    }


    public function getProvider():static|PaymentProviderContract
    {
        return $this;
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
        return  new RefundAction();
    }

    public function payout(): PaymentProviderPayoutContract
    {
        // TODO: Implement payout() method.
    }

    /**
     * @return PaymentProviderOrderContract
     */
    public function order(): PaymentProviderOrderContract
    {
        return new OrderAction($this);
    }
}
