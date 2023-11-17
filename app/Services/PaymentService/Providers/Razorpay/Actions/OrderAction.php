<?php

namespace App\Services\PaymentService\Providers\Razorpay\Actions;

use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderMethodContract;
use App\Services\PaymentService\Providers\Razorpay\RazorpayApi;
use Razorpay\Api\Api;


class OrderAction implements PaymentProviderMethodContract
{
    protected RazorpayApi $api;
    protected PaymentProviderContract $paymentProvider;

    public function __construct(RazorpayApi $api, PaymentProviderContract $paymentProvider)
    {
        $this->api = $api;
        $this->paymentProvider = $paymentProvider;
    }


    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->api->order->create($data);
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

    /**
     * @return mixed
     */
    public function verify()
    {
        // TODO: Implement verify() method.
    }
}
