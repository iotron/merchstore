<?php

namespace App\Services\PaymentService\Providers\Custom\Actions;

use App\Models\Order\Order;
use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderMethodContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderOrderContract;
use App\Services\PaymentService\Support\PaymentServiceHelper;
use Illuminate\Support\Str;

class OrderAction implements PaymentProviderOrderContract
{
    protected PaymentProviderContract $paymentProvider;

    public function __construct(PaymentProviderContract $paymentProvider)
    {
        $this->paymentProvider = $paymentProvider;
    }



    public function create(Order $order):mixed
    {
        $receipt = PaymentServiceHelper::newReceipt();

        return [
            'id' => 'custom_order_'.Str::replace('receipt_','',$receipt),
            'receipt' => $receipt,
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
