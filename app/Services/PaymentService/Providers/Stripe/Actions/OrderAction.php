<?php

namespace App\Services\PaymentService\Providers\Stripe\Actions;

use App\Models\Customer\Payment;
use App\Models\Order\Order;
use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderMethodContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderOrderContract;
use App\Services\PaymentService\Providers\Stripe\StripePaymentServiceContract;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class OrderAction implements PaymentProviderOrderContract
{

    protected StripeClient $api;
    protected PaymentProviderContract|StripePaymentServiceContract $paymentProvider;


    public function __construct(StripeClient $api_key,PaymentProviderContract $paymentProvider)
    {
        $this->api = $api_key;
        $this->paymentProvider = $paymentProvider;


    }


    /**
     * @param array $data
     * @return Session|PaymentIntent|null
     * @throws ApiErrorException
     */
    public function create(Order $order): Session|null|PaymentIntent
    {


        // Process Payment Intent
        if ($this->paymentProvider->isIntent())
        {

            if ($this->paymentProvider->isWallet())
            {
                $paymentMethod = $this->api->paymentMethods->create([
                    'type' => 'wallet',
                    'wallet' => [
                        'type' => $this->paymentProvider->getWalletType()
                    ],
                ]);
                // Update the $orderArray with the PaymentMethod ID.
                $data['payment_method'] = $paymentMethod->id;
            }

            return $this->api->paymentIntents->create($data);
        }

        // Process Checkout Session
        if ($this->paymentProvider->isCheckout())
        {
            return $this->api->checkout->sessions->create($data);
        }

        return null;
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
     * @param Payment $payment
     * @param array $data
     * @return bool
     */
    public function verifyWith(Payment $payment, array $data): bool
    {
        // TODO: Implement verify() method.
    }
}
