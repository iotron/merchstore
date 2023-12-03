<?php

namespace App\Services\PaymentService\Providers\Custom\Actions;

use App\Helpers\Money\Money;
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



    public function create(Order $order):array
    {

        $prefix = is_null(config('services.custom-payment.prefix')) ? '' : config('services.custom-payment.prefix');
        return  [
            'id' => $prefix.'order_'.$order->uuid,
            'entity' => 'order',
            'amount' => $order->total->getAmount(),
            'amount_paid' => 0,
            'amount_due' => $order->total->getAmount(),
            'currency' => $order->total->currency(),
            'receipt' => 'receipt#'.Str::upper(Str::random(2)).now()->timestamp,
            'offer_id' => null,
            'status'  => 'created',
            'attempts' => 0,
            'notes' => [
                'voucher' => $order->voucher,
                'quantity' => $order->quantity,
                'total' => $order->total->getAmount(),
            ],
            'created_at' => now()->timestamp,
            'error' => []

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
