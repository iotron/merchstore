<?php

namespace App\Services\PaymentService\Supports\Order;

use App\Services\PaymentService\Contracts\PaymentServiceContract;
use App\Services\PaymentService\Supports\Order\Provider\RazorpayOrder;
use App\Services\PaymentService\Supports\Order\Provider\StripeOrder;
use Illuminate\Database\Eloquent\Model;

/**
 * Core Order Builder
 * Build Order Array For
 * Individual Payment Providers
 */
class OrderBuilder implements OrderBuilderContract
{

    protected const AVAILABLE_ORDER_CLASSES = [
        'razorpay' => RazorpayOrder::class,
        'stripe' => StripeOrder::class
    ];

    protected ?PaymentServiceContract $paymentService;
    protected ?string $receipt = null;
    protected ?string $bookingName = null;
    protected ?string $bookingEmail = null;
    protected null|string|int $bookingContact = null;
    protected ?Model $subjectModel=null;
    protected array $items = [];

    protected OrderBuilderContract|null $orderClass=null;
    protected array $cartMeta = [];

    public function __construct(null|PaymentServiceContract $paymentService)
    {
        // Payment Service Can Be Null Only For Console Run
        throw_if(!app()->runningInConsole() && is_null($paymentService),'Payment Service Can\'t be null');
        $this->paymentService = $paymentService;
    }


    public function model(Model $model): static
    {
        throw_unless(isset($model->id),get_class($model).' missing column id');
        throw_unless(isset($model->name),get_class($model).' missing column name');
        throw_unless(isset($model->url),get_class($model).' missing column url');

        $this->subjectModel = $model;
        return $this;
    }

    public function items(array $items_array):static
    {
        $this->items = $items_array;
        return $this;
    }



    public function receipt(string $receipt): static
    {
        $this->receipt = $receipt;
        return $this;
    }

    public function bookingName(string $bookingName): static
    {
        $this->bookingName = $bookingName;
        return $this;
    }

    public function bookingEmail(string $bookingEmail): static
    {
        $this->bookingEmail = $bookingEmail;
        return $this;
    }

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
        throw_unless($this->keyExist($cart_meta,'total'),'missing total in cart meta');
        throw_unless($this->keyExist($cart_meta,'tickets'),'missing tickets in cart meta');
        throw_unless($this->keyExist($cart_meta,'coupon'),'missing coupon in cart meta');
        throw_unless($this->keyExist($cart_meta,'quantity'),'missing quantity in cart meta');
        throw_unless($this->keyExist($cart_meta,'subtotal'),'missing subtotal in cart meta');
        throw_unless($this->keyExist($cart_meta,'discount'),'missing discount in cart meta');
        throw_unless($this->keyExist($cart_meta,'tax'),'missing tax in cart meta');


        $this->cartMeta = $cart_meta;
        return $this;
    }

    protected function prepareOrderBuilderForProvider()
    {
        $providerName = !is_null($this->paymentService) ? $this->paymentService->getProviderName() : 'razorpay';

        $orderClass = self::AVAILABLE_ORDER_CLASSES[$providerName];
        $this->orderClass = new $orderClass($this->paymentService);

    }

    public function getArray():array
    {
        $this->prepareOrderBuilderForProvider();

        return $this->orderClass
            ->receipt($this->receipt)
            ->model($this->subjectModel)
            ->items($this->items)
            ->cartMeta($this->cartMeta)
            ->bookingName($this->bookingName)
            ->bookingEmail($this->bookingEmail)
            ->bookingContact($this->bookingContact)
            ->getArray();
    }

    protected function keyExist(array $array,string $key): bool
    {
        return array_key_exists($key,$array);
    }

}
