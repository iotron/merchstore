<?php

namespace App\Services\PaymentService\Providers\Razorpay\Actions;

use App\Models\Order\Order;
use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderMethodContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderOrderContract;
use App\Services\PaymentService\Providers\Razorpay\RazorpayApi;
use App\Services\PaymentService\Providers\Razorpay\Support\RazorpayOrderFormatter;
use App\Services\PaymentService\Support\PaymentServiceHelper;
use Razorpay\Api\Api;


class OrderAction implements PaymentProviderOrderContract
{
    protected RazorpayApi $api;
    protected PaymentProviderContract $paymentProvider;

    public function __construct(RazorpayApi $api, PaymentProviderContract $paymentProvider)
    {
        $this->api = $api;
        $this->paymentProvider = $paymentProvider;
    }


    /**
     * @param Order $order
     * @return array
     */
    public function create(Order $order):array
    {
        $formattedData = [
        'receipt' => PaymentServiceHelper::newReceipt(),
        'amount' => $order->total->getAmount(),
        'currency' => $order->total->getCurrency()->getCurrency(),

            'notes' => [
                'customer_name' => $order->customer->name,
                'customer_email' => $order->customer->email,
                'customer_contact' => $order->customer->contact,
                // column
                'voucher' => $order->voucher,
                'quantity' => $order->quantity,
                'subtotal' => $order->subtotal->getAmount(),
                'discount' => $order->discount->getAmount(),
                'tax' => $order->tax->getAmount(),
                'total' => $order->total->getAmount(),
            ],
    ];


        return $this->api->order->create($formattedData)->toArray();
    }

    /**
     * @param string|int $id
     * @return mixed
     */
    public function fetch(int|string $id)
    {
        return $this->api->order->fetch($id);
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
