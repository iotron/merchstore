<?php

namespace App\Services\OrderService;

use App\Helpers\Cart\Cart;
use App\Models\Customer\Customer;
use App\Models\Localization\Address;
use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentProvider;
use App\Models\Product\Product;
use App\Services\PaymentService\Contracts\PaymentServiceContract;
use App\Services\OrderService\Supports\Order\OrderBuilder;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderCreationService
{

    protected Cart $cart;
    protected ?PaymentServiceContract $paymentService;
    protected string $receipt;
    protected array $cartMeta = [];
    protected Authenticatable|Customer $customer;
    protected string $provider;
    protected ?Order $order=null;
    protected bool $isCod = false;

    public function __construct(PaymentServiceContract|null $paymentService, Cart $cart)
    {
        $this->paymentService = $paymentService;
        $this->cart = $cart;
        $this->cartMeta = $this->cart->getMeta();
        $this->cart->getCustomer()->loadMissing('payments');
        $this->receipt = self::getUniqueReceiptID($this->cart->getCustomer()->payments);
        $this->customer  = $this->cart->getCustomer();
        $this->provider = $this->paymentService->provider()->getClass();
        $this->isCod = $this->paymentService->getProviderModel()->url == PaymentProvider::COD ;
    }

    public function checkout(Address $billing_address)
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




        // Create New Order Via Payment Provider Based On Order Array
        $newOrder = $this->paymentService->provider()->order()->create($orderArray);

        // Create An Pending Payment Based On Provider New Order Data
        $payment = $this->createAnPendingPayment($newOrder);
        // Create An Pending Order Based On Newly Created Payment
        $this->order = $this->createAnPendingOrder($payment,$billing_address);
        // Finish Pending Payment Process And Update Payment With Booking ID
        $payment->order_id = $this->order->id;
        $payment->save();

        if($this->isCod)
        {
            $this->updateProductStock();
        }

        // Attaching Products To Order
        $this->attachProductsToOrder();




        // Clean Up Cart
        $this->cart->reset();

        // return Application Checkout link Route
        return (!app()->runningInConsole() && !is_null($this->paymentService)) ? response()->json([
            'success' => true,
            'message' => 'order placed successfully',
            'payment_provider_url' => $this->paymentService->getProviderModel()->url,
            'order' => [
                'uuid' => $this->order->uuid,
                'status' => $this->order->status,
            ],
            'redirect' => ($this->paymentService->getProviderModel()->url == PaymentProvider::COD) ?
                        config('app.client_url').'/orders/'.$this->order->uuid : route('payment.visit', ['payment' => $payment->receipt]),
        ], 200) : ['success' => true, 'message' => 'order placed successfully', 'payment' => $payment];



    }


    /**
     * @param array|object $newOrder
     * @return Payment|Model
     */
    protected function createAnPendingPayment(array|object $newOrder):Payment|Model
    {
        return $this->cart->getCustomer()->payments()->create([
            'receipt' => $this->receipt,
            'provider_gen_id' => $newOrder['id'],
            'provider_class' => $this->provider,
            'promo_code' => $this->cartMeta['coupon'] ?? '',
            'quantity' => $this->cartMeta['quantity'],
            'subtotal' => $this->cartMeta['subtotal']->getAmount(),
            'discount' => $this->cartMeta['discount']->getAmount(),
            'tax' => $this->cartMeta['tax']->getAmount(),
            'total' => $this->cartMeta['total']->getAmount(),
            'details' => is_object($newOrder) ? $newOrder->toArray() : $newOrder,
            'expire_at' => (!$this->isCod) ? now()->addMinutes(config('app.booking_cleanup_time_limit')) : now()->addMonth(),
            'payment_provider_id' => $this->paymentService->getProviderModel()->id,
        ]);
    }


    private function createAnPendingOrder(Model|Payment $payment,Address $billingAddress)
    {
        $uuid = self::getUniqueOrderID($this->customer->orders);
        return $this->customer->orders()->create([
                'uuid' => $uuid,
                'amount' => $payment->total,
                'subtotal' => $payment->subtotal,
                'discount' => $payment->discount,
                'tax' => $payment->tax,
                'total' => $payment->total,
                'quantity' => $payment->quantity,
                'voucher' => $payment->voucher,
                'status' => (!$this->isCod) ? Order::PENDING : Order::CONFIRM,
                'payment_success' => false,
                'expire_at' => (!$this->isCod) ? now()->addMinutes(config('app.booking_cleanup_time_limit')) : now()->addMonth(),
                'customer_id' => $this->customer->id,
                'payment_provider_id' => $this->paymentService->getProviderModel()->id,
                'customer_gstin' => null, // need data here
                'shipping_is_billing' => false,
                'billing_address_id' => $billingAddress->id,
//                'address_id' => '', // need to check
            ]);

    }


    protected function attachProductsToOrder(): void
    {
            $records = [];
            foreach ($this->cartMeta['products'] as $item)
            {
                $records [] = [
                    'quantity' => $item['pivot_quantity'],
                    'amount' => $item['total_base_amount']->getAmount(),
                    'discount' => $item['total_discount_amount']->getAmount(),
                    'tax' => $item['total_tax_amount']->getAmount(),
                    'total' => $item['net_total']->getAmount(),
                    'product_id' => $item['id']
                ];
            }

            $this->order->orderProducts()->createMany($records);
    }


    protected function updateProductStock()
    {
        foreach ($this->cartMeta['products'] as $item)
        {
            $productModel = $item['product'];

        }
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

    protected static function getUniqueOrderID(object $orderArray): string
    {
        $uid = ucwords(Str::random(6));
        $result = $orderArray->contains('uuid', $uid);
        return (!$result) ? $uid : self::getUniqueOrderID($orderArray);
    }



}
