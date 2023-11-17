<?php

namespace App\Services\PaymentService\Providers\CashOnDelivery\Actions;

use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderMethodContract;
use Illuminate\Support\Str;

class OrderAction implements PaymentProviderMethodContract
{
    protected PaymentProviderContract $paymentProvider;

    public function __construct(PaymentProviderContract $paymentProvider)
    {
        $this->paymentProvider = $paymentProvider;
    }


    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return array_merge($data,['id' => 'order_'.Str::random(5)]);
    }

    /**
     * @param string|int $id
     * @return mixed
     */
    public function fetch(int|string $id)
    {
        // TODO: Implement fetch() method.
    }

    /**
     * @return mixed
     */
    public function all()
    {
        // TODO: Implement all() method.
    }
}
