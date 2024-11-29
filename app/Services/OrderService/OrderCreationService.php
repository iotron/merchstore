<?php

namespace App\Services\OrderService;

use App\Helpers\Cart\Cart;
use App\Models\Localization\Address;
use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentProvider;
use App\Services\Iotron\LaravelRazorpay\LaravelRazorpay;
use App\Services\MoneyServices\Money;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class OrderCreationService
{

    protected Cart $cart;
    protected ?Order $order = null;
    protected ?Payment $payment = null;
    protected ?Address $shippingAddress = null;
    protected ?Address $billingAddress = null;
    protected array $cartMeta;
    protected ?string $provider = null;
    protected ?string $redirectUrl = null;





    public static function make(): static
    {
        return new static();
    }

    public function create(\App\Helpers\Cart\Cart $cart): static
    {
        $this->cart = $cart;
        return $this;
    }

    public function setCartMeta(array $cartMeta): static
    {
        $this->cartMeta = $cartMeta;
        return $this;
    }

    public function setProvider(string $provider): static
    {
        $this->provider = $provider;
        return $this;
    }

    public function redirectUrl(string $redirect_url): static
    {
        $this->redirectUrl = $redirect_url;
        return $this;
    }

    public function setShippingAddress(Address $shippingAddress): static
    {
        $this->shippingAddress = $shippingAddress;
        return $this;
    }

    public function setBillingAddress(Address $billingAddress): static
    {
        $this->billingAddress = $billingAddress;
        return $this;
    }


    /**
     * Get Newly Placed
     * Order Checkout Info
     * @param bool $isPanelCheckout
     * @return JsonResponse|array
     */
    public function checkout(bool $isPanelCheckout = false): JsonResponse|array
    {

        if($this->processCheckout())
        {
            return $isPanelCheckout ? [
                'provider' => $this->provider,
                'cart' => $this->cart,
                'meta' => $this->cartMeta,
                'order' => [
                    'id' => $this->order?->id,
                    'uuid' => $this->order?->uuid,
                    'model' => $this->order
                ],
                'payment' => $this->payment,
                'address' => [
                    'shipping' => $this->shippingAddress,
                    'billing' => $this->billingAddress,
                ],
            ] :$this->getSuccessCheckoutResponse();
        }else{
            $errorMessage = [
                'status' => false,
                'message' => 'Order not placed successfully!'
            ];
            return $isPanelCheckout ? $errorMessage : response()->json($errorMessage, 400);
        }
    }


    private function getSuccessCheckoutResponse():JsonResponse
    {
        // Response Returns
        return response()->json([
            'success' => true,
            'message' => $this->provider == PaymentProvider::CASH ? 'order confirmed successfully' :'order placed successfully',
            'redirect' => $this->provider == PaymentProvider::CASH ? $this->getRedirectUrls()['success_url'] : route('checkout', ['payment' => $this->payment->provider_gen_id]),
            'order_uuid' => $this->order->uuid,
            'provider' => $this->provider,
        ]);

    }




    /**
     * Handel Checkout
     * @return bool
     */
    protected function processCheckout(): bool
    {

        // Create Order
        $this->order = $this->createOrder();

        // New Order Request From Payment Provider
        $providerOrderArray = [];
        if ($this->provider != PaymentProvider::CASH)
        {
            // init provider order for payment
            $providerOrderArray = $this->getGeneratedProviderOrder();
        }

        // Make Payment For Order
        $this->payment = $this->createAnPendingPayment($providerOrderArray);

        // Attach Products

        $this->attachingProductIntoOrderProduct();





        if ($this->provider == PaymentProvider::CASH)
        {
            // Confirm Cash On Delivery Order

            $orderConfirmService = OrderConfirmService::make($this->order);
            $orderConfirmService->validate();
        }

        // Clean up Cart
        $this->cart->reset();

        return !is_null($this->order);
    }


    // Helper Methods

    private function getDefaultRedirectUrl(): string
    {
        return config('app.client_url').'/orders/'.$this->order->id;
    }

    private function getRedirectUrls(): array
    {
        return [
            'callback_url' => route('confirm.checkout.order', ['order' => $this->order->uuid]),
            'success_url' => ! is_null($this->redirectUrl) ? $this->redirectUrl : config('app.client_url').'/orders/'.$this->order->uuid,
            'failure_url' => ! is_null($this->redirectUrl) ? $this->redirectUrl : config('app.client_url').'/cart/',
        ];
    }

    private static function getUniqueBookingID(object $bookingArray): string
    {
        $uid = ucwords(Str::random(6));
        $result = $bookingArray->contains('uuid', $uid);
        return (! $result) ? $uid : self::getUniqueBookingID($bookingArray);
    }




    // Order Process

    protected function createOrder():Order
    {
        $uuid = $this->generateUniqueID();

        return $this->cart->getCustomer()->orders()->create([
            'uuid' => $uuid,
            'voucher' => $this->cartMeta['coupon'],
            'quantity' => $this->cartMeta['quantity'],
            'amount' => $this->cartMeta['total']->getValue(),
            'subtotal' => $this->cartMeta['subtotal']->getValue(),
            'discount' => $this->cartMeta['discount']->getValue(),
            'tax' => $this->cartMeta['tax']->getValue(),
            'total' => $this->cartMeta['total']->getValue(),
            'status' => ($this->provider != PaymentProvider::CASH) ? Order::PENDING : Order::CONFIRM,
            'payment_success' => false,
            'expire_at' => ($this->provider == PaymentProvider::CASH) ? now()->addMonth() : now()->addMinutes((int)config('services.defaults.order_cleanup_time_limit')),
//            'customer_id' => $this->cart->getCustomer()->id,
            'customer_gstin' => null, // need data here
            'shipping_is_billing' => $this->shippingAddress->id == $this->billingAddress->id,
            'billing_address_id' => $this->billingAddress->id,
            'shipping_address_id' => $this->shippingAddress->id,
            'is_cod' => $this->provider == PaymentProvider::CASH,
        ]);
    }













    // Provider Order And Payment Creation

    private function getGeneratedProviderOrder(): JsonResponse|array
    {



        $responseArray = LaravelRazorpay::make()->order()->create([
            'receipt' => $this->order->uuid,
            'amount' => (integer) $this->cartMeta['net_total_amount'],
            'currency' => $this->cartMeta['currency'],
        ]);


        if (!$responseArray['success'])
        {
            return response()->json([
                'success' => $responseArray['success'],
                'message' => $responseArray['error'],
            ],400);
        }

        if (is_null($responseArray['data']['payment_provider_id']))
        {
            $responseArray['data']['payment_provider_id'] = $this->order->payment_provider_id;
        }

        $responseArray['data'] = array_merge($responseArray['data'],[
            'details' => array_merge($responseArray['data']['details'],[
                'additional' => [
                    'currency' => $this->cartMeta['currency'],
                    'buyer_email' => $this->cart->getCustomer()->email,
                    'buyer_name' => $this->cart->getCustomer()->name,
                    'buyer_contact' => $this->cart->getCustomer()->contact,
                ],
            ]),
            'callback_url' => $this->getRedirectUrls()['callback_url'],
            'success_url' => $this->getRedirectUrls()['success_url'],
            'failure_url' => $this->getRedirectUrls()['failure_url'],
        ]);


        return $responseArray['data'];
    }

    private function createAnPendingPayment(array|object $newProviderOrder)
    {
        return $this->order->payment()->create(array_merge($newProviderOrder, [
            'expire_at' => now()->addMinutes((int) config('services.defaults.order_cleanup_time_limit')),
        ]));
    }



    // Methods

    protected function generateUniqueID()
    {
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Custom character set
        $prefix = now()->format('dHis'); // Timestamp prefix
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $random = substr(str_shuffle(str_repeat($characters, 4)), 0, 4);
            $id = $prefix.$random;
            $attempt++;
        } while (Order::where('uuid', $id)->exists() && $attempt < $maxAttempts);

        if ($attempt == $maxAttempts) {
            //throw new Exception('Unable to generate unique ID');
            return null;
        }

        return $id;
    }

    private function attachingProductIntoOrderProduct()
    {
        foreach ($this->cartMeta['products'] as $product)
        {
            $productPrice = $product['price']; // money instance carrier
            $discountPrice = $product['discount'] ?? new Money();
            $priceAfterDiscount = $productPrice->multiplyOnce($product['pivot_quantity'])->subOnce($discountPrice);
            $taxAmount = new Money();
            if ($priceAfterDiscount->greaterThanOrEqual(new Money(500)))
            {
                $taxAmount->add($priceAfterDiscount->multiplyOnce($product['pivot_quantity'])->multiplyOnce($product['tax_percent'])->divideOnce(100));
            }
            $total = new Money();
            $total->add($priceAfterDiscount)->add($taxAmount);

            $this->order->orderProducts()->create([
                'quantity' => $product['pivot_quantity'],
                'amount' => $productPrice->multiply($product['pivot_quantity'])->getValue(),
                'discount' => $discountPrice->getValue(),
                'tax' => $taxAmount->getValue(),
                'total' => $total->getValue(),
                'product_id' => $product['id'],
            ]);

        }
    }


}
