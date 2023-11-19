<?php

namespace App\Services\OrderService\Supports\Order\Provider;

use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\PaymentServiceContract;
use App\Services\PaymentService\Providers\Razorpay\RazorpayPaymentServiceContract;
use App\Services\OrderService\Supports\Order\OrderBuilderContract;
use Illuminate\Database\Eloquent\Model;

class RazorpayOrder implements OrderBuilderContract
{

    protected ?string $receipt = null;
    protected ?string $bookingName = null;
    protected ?string $bookingEmail = null;
    protected null|string|int $bookingContact = null;
    protected ?Model $subjectModel=null;
    protected array $items = [];
    protected array $cartMeta = [];





    protected null|PaymentProviderContract|RazorpayPaymentServiceContract $paymentProvider=null;


    public function __construct(null|PaymentServiceContract|RazorpayPaymentServiceContract $paymentProvider)
    {
        $this->paymentProvider = !is_null($paymentProvider) ? $paymentProvider->provider() : null;
    }


    /**
     * @param ?Model $model
     * @return $this
     */
    public function model(?Model $model): static
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
        return [
            'receipt' => $this->receipt,
            'amount' => $this->cartMeta['total']->getAmount(),
            'currency' => $this->cartMeta['total']->getCurrency()->getCurrency(),

            'notes' => [
                'booking_name' => $this->bookingName,
                'booking_email' => $this->bookingEmail,
                'booking_contact' => $this->bookingContact,
                'model_id' => !is_null($this->subjectModel) ? $this->subjectModel->id : null,
                'products_ids' => implode(',', $this->cartMeta['products']->pluck('id')->toArray()),
                'product_details' => json_encode($this->items),
                // Currently Not Necessary (Remove When Update Livewire)
                'voucher' => $this->cartMeta['coupon'] ?? '',
                // column
                'quantity' => $this->cartMeta['quantity'],
                'subtotal' => $this->cartMeta['subtotal']->getAmount(),
                'discount' => $this->cartMeta['discount']->getAmount(),
                'tax' => $this->cartMeta['tax']->getAmount(),
                'total' => $this->cartMeta['total']->getAmount(),
            ],
        ];
    }

}
