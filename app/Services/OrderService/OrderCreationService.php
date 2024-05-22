<?php

namespace App\Services\OrderService;

use App\Models\Customer\Customer;
use App\Models\Localization\Address;
use App\Models\Order\Order;
use App\Models\Order\OrderProduct;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentProvider;
use App\Services\Iotron\MoneyService\Money;
use App\Services\PaymentService\Contracts\PaymentProviderContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class OrderCreationService
{
    protected PaymentProviderContract $paymentProvider;

    protected bool $isCod = false;

    protected ?string $error = null;

    protected ?string $token = null;

    protected ?Address $shippingAddress = null;

    protected ?Address $billingAddress = null;

    protected Order $order;

    protected null|Authenticatable|Customer $customer = null;

    protected array $cartMeta = [];

    protected Payment|null|Model $payment = null;

    protected array $codProducts = [];

    public function __construct(PaymentProviderContract $paymentProvider, Authenticatable|Customer $customer, array $cartMeta)
    {
        $this->paymentProvider = $paymentProvider;
        $this->isCod = ($this->paymentProvider->getProviderName() == PaymentProvider::CUSTOM);
        $this->customer = $customer;
        $this->cartMeta = $cartMeta;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function placeOrder(string $orderToken, Address $shippingAddress, Address $billing_address): void
    {
        // Fill First
        $this->token = $orderToken;
        $this->shippingAddress = $shippingAddress;
        $this->billingAddress = $billing_address;

        // Start Process
        // Step 1
        $this->order = $this->makeAnOrder();
        // Step 2
        $this->payment = $this->makePayment();
        // Step 3
        if (! is_null($this->payment)) {
            $this->attachProducts();
        }

        // Only For CashOnDelivery Order
        if ($this->isCod && is_null($this->error)) {
            // Order Will Be Confirmed After Order Placed
            $orderConfirmService = new OrderConfirmService($this->payment);
            $orderConfirmService->confirmOrder();
            $this->error = $orderConfirmService->getError();
        }

    }

    public function isCashOnDelivery(): bool
    {
        return $this->isCod;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    /**
     * Step 1
     */
    protected function makeAnOrder(): Order
    {
        return $this->customer->orders()->create([
            'uuid' => $this->token,
            'voucher' => $this->cartMeta['coupon'],
            'quantity' => $this->cartMeta['quantity'],
            'amount' => $this->cartMeta['total'],
            'subtotal' => $this->cartMeta['subtotal'],
            'discount' => $this->cartMeta['discount'],
            'tax' => $this->cartMeta['tax'],
            'total' => $this->cartMeta['total'],
            'status' => (! $this->isCod) ? Order::PENDING : Order::CONFIRM,
            'payment_success' => false,
            'expire_at' => ($this->isCod) ? now()->addMonth() : now()->addMinutes(config('services.defaults.order_cleanup_time_limit')),
            'customer_id' => $this->customer->id,
            'customer_gstin' => null, // need data here
            'shipping_is_billing' => $this->shippingAddress->id == $this->billingAddress->id,
            'billing_address_id' => $this->billingAddress->id,
            'shipping_address_id' => $this->shippingAddress->id,
            'is_cod' => $this->isCod,
        ]);
    }

    /**
     * Step 2
     */
    private function makePayment(): Payment|Model|null
    {
        // Now Make AN Order On Payment Provider Based On This Order
        $newOrder = $this->paymentProvider->order()->create($this->order);

        if (isset($newOrder['error']) && ! empty($newOrder['error'])) {
            if (isset($newOrder['error']['description']) && isset($newOrder['error']['reason'])) {
                $this->error = $newOrder['error']['description'].' |reason : '.$newOrder['error']['reason'];
            }

            return null;
        } else {
            return $this->order->payment()->create([
                'receipt' => 'receipt_'.$this->order->uuid,
                'provider_gen_id' => $newOrder['id'],
                'provider_class' => $this->paymentProvider->getClass(),
                'voucher' => $this->order->voucher,
                'quantity' => $this->order->quantity,
                'subtotal' => $this->order->subtotal,
                'discount' => $this->order->discount,
                'tax' => $this->order->tax,
                'total' => $this->order->total,
                'details' => is_object($newOrder) ? $newOrder->toArray() : $newOrder,
                'expire_at' => ($this->isCod) ? now()->addMonth() : now()->addMinutes(config('services.defaults.order_cleanup_time_limit')),
                'payment_provider_id' => $this->paymentProvider->getModel()->id,
                'customer_id' => $this->customer->id,
            ]);
        }

    }

    /**
     * Step 3
     */
    protected function attachProducts(): void
    {
        foreach ($this->cartMeta['products'] as $productArray) {
            $newOrderProduct = $this->makeOrderProduct($productArray);
        }
    }

    /**
     * Step 3.1
     */
    protected function makeOrderProduct(array $item): OrderProduct|Model
    {
        $discountAmount = isset($item['total_discount_amount']) ? $item['total_discount_amount'] : new Money(0.0);

        return $this->order->orderProducts()->create([
            'quantity' => $item['pivot_quantity'],
            'amount' => ($item['total_base_amount'] instanceof Money) ? $item['total_base_amount']->getAmount() : $item['total_base_amount'],
            'discount' => ($discountAmount instanceof Money) ? $discountAmount->getAmount() : $discountAmount,
            'tax' => ($item['total_tax_amount'] instanceof Money) ? $item['total_tax_amount']->getAmount() : $item['total_tax_amount'],
            'total' => ($item['net_total'] instanceof Money) ? $item['net_total']->getAmount() : $item['net_total'],
            'product_id' => $item['id'],
        ]);
    }
}
