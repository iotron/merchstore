<?php

namespace App\Services\Iotron\LaravelPayments\Contracts\Providers;


use App\Models\Payment\PaymentProvider;
use App\Services\Iotron\LaravelPayments\Contracts\Builder\OrderMakerContract;
use App\Services\Iotron\LaravelPayments\Contracts\Models\PaymentModelContract;
use Illuminate\Database\Eloquent\Model;

interface LaravelPaymentProviderContract
{
    public function __construct(PaymentProvider $paymentProvider, array $auth);

    public function get(): static;

    public function getApi();

    public function getModel(): Model|PaymentProvider;

    public function order(?OrderMakerContract $orderMaker);

    public function verify(?PaymentModelContract $payment_model = null);
}
