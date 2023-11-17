<?php

namespace App\Services\OrderService;

use App\Helpers\Cart\Cart;
use App\Models\Customer\Customer;
use App\Services\PaymentService\Contracts\PaymentServiceContract;
use App\Services\PaymentService\Supports\Order\OrderBuilder;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

class OrderCreationService
{

    protected Cart $cart;
    protected ?PaymentServiceContract $paymentService;
    protected string $receipt;
    protected array $cartMeta = [];
    protected Authenticatable|Customer $customer;

    public function __construct(PaymentServiceContract|null $paymentService, Cart $cart)
    {
        $this->paymentService = $paymentService;
        $this->cart = $cart;
        $this->cartMeta = $this->cart->getMeta();
        $this->cart->getCustomer()->loadMissing('payments');
        $this->receipt = self::getUniqueReceiptID($this->cart->getCustomer()->payments);
        $this->customer  = $this->cart->getCustomer();
    }

    public function checkout()
    {
        if ($this->cart->getErrors()) {
            return response()->json(['success' => false, 'message' => $this->cart->getErrors()], 403);
        }
        // Prepare An Order Array
        $orderBuilder = new OrderBuilder($this->paymentService);
        $orderArray = $orderBuilder
            ->model(null)
            ->receipt($this->receipt)
            ->items($this->getProductArray())
            ->cartMeta($this->cartMeta)
            ->bookingName($this->customer->name)
            ->bookingEmail($this->customer->email)
            ->bookingContact($this->customer->contact)
            ->getArray();


        dd($orderArray);





    }





    /**
     * @param object $collection
     * @return string
     */
    protected static function getUniqueReceiptID(object $collection): string
    {
        $uid = 'receipt_'.ucwords(Str::random(6));
        $result = $collection->contains('receipt', $uid);
        return (!$result) ? $uid : self::getUniqueReceiptID($collection);
    }

    protected function getProductArray()
    {
        return $this->cartMeta['products']->keyBy('id')->map(function ($item) {
            return ['quantity' => $item['pivot_quantity']];
        })->toArray();
    }


}
