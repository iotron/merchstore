<?php

namespace App\Services\PaymentService\Supports\Order\Provider;

use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\PaymentServiceContract;
use App\Services\PaymentService\Providers\Stripe\StripePaymentServiceContract;
use App\Services\PaymentService\Supports\Order\OrderBuilderContract;
use Illuminate\Database\Eloquent\Model;

class StripeOrder implements OrderBuilderContract
{

    protected ?string $receipt = null;
    protected ?string $bookingName = null;
    protected ?string $bookingEmail = null;
    protected null|string|int $bookingContact = null;
    protected ?Model $subjectModel=null;
    protected array $items = [];
    protected array $cartMeta = [];
    protected null|PaymentProviderContract|StripePaymentServiceContract $paymentProvider;


    public function __construct(null|PaymentServiceContract|StripePaymentServiceContract $paymentProvider)
    {
        $this->paymentProvider = !is_null($paymentProvider) ? $paymentProvider->provider() : null;
    }



    /**
     * @param Model $model
     * @return $this
     */
    public function model(Model $model): static
    {
        $this->subjectModel = $model;
        return $this;
    }

    /**
     * @param array $items_array
     * @return $this
     */
    public function items(array $items_array):static
    {
        $this->items = $items_array;
        return $this;
    }


    /**
     * @param string $receipt
     * @return $this
     */
    public function receipt(string $receipt): static
    {
        $this->receipt = $receipt;
        return $this;
    }


    /**
     * @param string $bookingName
     * @return $this
     */
    public function bookingName(string $bookingName): static
    {
        $this->bookingName = $bookingName;
        return $this;
    }

    /**
     * @param string $bookingEmail
     * @return $this
     */
    public function bookingEmail(string $bookingEmail): static
    {
        $this->bookingEmail = $bookingEmail;
        return $this;
    }

    /**
     * @param int|string $bookingContact
     * @return $this
     */
    public function bookingContact(int|string $bookingContact): static
    {
        $this->bookingContact = $bookingContact;
        return $this;
    }

    /**
     * @param array $cart_meta
     * @return $this
     */
    public function cartMeta(array $cart_meta): static
    {
        $this->cartMeta = $cart_meta;
        return $this;
    }
    /**
     * @return array
     */
    public function getArray(): array
    {
        return $this->prepareOrderArray();
    }



    protected function prepareOrderArray(): array
    {
       $data = [];
        if ($this->paymentProvider->isIntent())
        {
            $data = array_merge($data,$this->prepareIntentOrder($data));
        }

        if ($this->paymentProvider->isCheckout())
        {
            $data = array_merge($data,$this->prepareCheckoutSessionOrder($data));
        }
        return $data;
    }




    protected function prepareIntentOrder(array $data): array
    {

        $data = array_merge($data,[
            'amount' => $this->cartMeta['total']->getAmount(),
            'currency' => $this->cartMeta['currency'],
            'description' => 'Payment for order',
            'statement_descriptor' => 'Order',
            'confirmation_method' => 'automatic',
            'capture_method' => 'automatic',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
            'receipt_email' => $this->cartMeta['customer'],
        ]);

        if ( $this->paymentProvider->isCard())
        {
            $data = array_merge($data,[
                'payment_method_types' => ['card'],
                'payment_method' => 'card',
            ]);
        }

        if ($this->paymentProvider->isSubscribable())
        {
            $data['setup_future_usage'] = 'off_session';
        }

        if ($this->paymentProvider->canTakeTransactionCharge())
        {
            $data['application_fee_amount'] = $this->paymentProvider->getTransactionFee();
        }
        return array_merge($data,$this->getStripeMeta('intent'));
    }



    protected function prepareCheckoutSessionOrder(array $data): array
    {

        // Prepare Items
        $result = $this->cartMeta['tickets']->map(function ($ticket){
            return [
                'price_data' => [
                    'currency' => $this->cartMeta['currency'],
                    'product_data' => [
                        'name' => $ticket['name'],
                    ],
                    'unit_amount' => $ticket['net_total']->getAmount(),
                ],

                'quantity' => $ticket['pivot_quantity'],
            ];
        })->toArray();


        $data = array_merge($data,[
            'line_items' => $result
        ]);


        $data = array_merge($data,[
            'customer_email' => $this->cartMeta['customer'],
            'billing_address_collection' => 'auto',
            'locale' => 'en',
            'submit_type' => 'auto',
            'client_reference_id' => $this->receipt,
            'mode'  => 'payment',
            'currency' => $this->cartMeta['currency'],
            'success_url' => route('confirm.payment', ['payment' => $this->receipt]).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.visit',['payment' => $this->receipt]),


        ]);

        if ($this->paymentProvider->displayTerms())
        {
            $data = array_merge($data,[
                'consent_collection' => [
                    'terms_of_service' => 'required',
                ],
                'custom_text' => [
                    'terms_of_service_acceptance' => [
                        'message' => 'I agree to the [Terms of Service]('.config('app.client_url').'/policy/tnc)',
                    ],
                    'submit' => ['message' => 'We\'ll email you instructions about booking.'],
                ],
            ]);
        }


        return array_merge($data,$this->getStripeMeta('checkout'));
    }



    protected function getStripeMeta(string $mode): array
    {
        return [
            'metadata' => [
                'receipt' => $this->receipt,
                'booking_name' => $this->bookingName,
                'booking_email' => $this->bookingEmail,
                'booking_contact' => $this->bookingContact,
                'event_id' => $this->subjectModel->id,
                'event_name' => $this->subjectModel->name,
                'event_url' => $this->subjectModel->url,
                'tickets_ids' => implode(',', $this->cartMeta['tickets']->pluck('id')->toArray()),
                'tickets_details' => json_encode($this->items),
                'promo' => $this->cartMeta['coupon'] ?? '',
                // column
                'quantity' => $this->cartMeta['quantity'],
                'subtotal' => $this->cartMeta['subtotal']->getAmount(),
                'discount' => $this->cartMeta['discount']->getAmount(),
                'tax' => $this->cartMeta['tax']->getAmount(),
                'total' => $this->cartMeta['total']->getAmount(),
                'provider_name' => 'stripe',
                'provider_mode' => $mode,
            ],
        ];
    }



}
