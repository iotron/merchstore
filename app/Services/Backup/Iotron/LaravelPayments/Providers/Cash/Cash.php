<?php

namespace App\Services\Iotron\LaravelPayments\Providers\Cash;

use App\Models\Payment\PaymentProvider;
use App\Services\Iotron\LaravelPayments\Contracts\Builder\OrderMakerContract;
use App\Services\Iotron\LaravelPayments\Contracts\Models\PaymentModelContract;
use App\Services\Iotron\LaravelPayments\Contracts\Models\PaymentProviderModelContract;
use App\Services\Iotron\LaravelPayments\Contracts\Providers\LaravelPaymentProviderContract;
use App\Services\Iotron\LaravelPayments\Providers;
use Illuminate\Database\Eloquent\Model;

class Cash implements LaravelPaymentProviderContract
{
    protected Model|PaymentProviderModelContract|PaymentProvider $model;

    protected Cash $api;

    public function __construct(PaymentProviderModelContract|PaymentProvider $paymentProvider, array $auth)
    {
        $this->model = $paymentProvider;
        $this->api = $this;
    }

    public function getApi(): static
    {
        return $this->api;
    }

    public function getModel(): Model|PaymentProvider
    {
        return $this->model;
    }

    public function get(): static
    {
        return $this;
    }

    public function order(?OrderMakerContract $orderMaker): Action\OrderAction
    {
        return new Providers\Cash\Action\OrderAction($this, $orderMaker->toArray());
    }

    /**
     * @return mixed
     */
    public function verify(?PaymentModelContract $payment_model = null): Action\VerifyAction
    {
        // TODO: Implement verify() method.
    }
}
