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
use App\Models\Order\OrderShipment;
use App\Models\Order\OrderInvoice;
use App\Models\Shipping\ShippingProvider;

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
    protected array $stockBag = [];

    public function __construct(PaymentServiceContract|null $paymentService, Cart $cart)
    {
        $this->paymentService = $paymentService;
        $this->cart = $cart;
        $this->cartMeta = $this->cart->getMeta();
        $this->cart->getCustomer()->loadMissing('payments');
        $this->receipt = self::getUniqueReceiptID($this->cart->getCustomer()->payments);
        $this->customer  = $this->cart->getCustomer();
        $this->provider = $this->paymentService->provider()->getClass();
        $this->isCod = $this->paymentService->getProviderModel()->code == PaymentProvider::COD ;
    }

    public function checkout(Address $shippingAddress,Address $billing_address): \Illuminate\Http\JsonResponse|array
    {
        if ($this->cart->getErrors()) {
            return response()->json(['success' => false, 'message' => $this->cart->getErrors()], 403);
        }

        $uuid = self::getUniqueOrderID($this->customer->orders);
        // Create An Pending Order Based On Newly Created Payment
        $this->order =  $this->customer->orders()->create([
            'uuid' => $uuid,
            'voucher' => $this->cartMeta['coupon'] ?? '',
            'quantity' => $this->cartMeta['quantity'],
            'amount' => $this->cartMeta['total']->getAmount(),
            'subtotal' => $this->cartMeta['subtotal']->getAmount(),
            'discount' => $this->cartMeta['discount']->getAmount(),
            'tax' => $this->cartMeta['tax']->getAmount(),
            'total' => $this->cartMeta['total']->getAmount(),
            'status' => (!$this->isCod) ? Order::PENDING : Order::CONFIRM,
            'payment_success' => false,
            'expire_at' => ($this->isCod) ? now()->addMonth() : now()->addMinutes(config('services.defaults.order_cleanup_time_limit')),
            'customer_id' => $this->customer->id,
            'customer_gstin' => null, // need data here
            'shipping_is_billing' => false,
            'billing_address_id' => $billing_address->id,
            'shipping_address_id' => $shippingAddress->id
        ]);




        // Prepare An Order Array (Provider Case) (code shorten)
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

        // Create New Order Via Payment Provider Based On Order Array (Provider Case)
        $newOrder = $this->paymentService->provider()->order()->create($orderArray);

        // Create An Pending Payment Based On Provider New Order Data (DB Case)
        $payment = $this->createAnPendingPayment($newOrder);


        if($this->isCod)
        {
            // Update Product Stock
            $this->updateProductStock();
        }

        // Attaching Products To Order
        $this->attachProductsToOrder();

        // Create Shipment  & Invoice Of ThisOrder

        $this->makeOrderShipmentWithInvoice($shippingAddress);




        // Clean Up Cart
        $this->cart->reset();

        // return Application Checkout link Route
        return (!app()->runningInConsole() && !is_null($this->paymentService)) ? response()->json([
            'success' => true,
            'message' => 'order placed successfully',
            'payment_provider' => [
                'name' => $this->paymentService->getProviderModel()->name,
                'code' => $this->paymentService->getProviderModel()->code,
            ],
            'order' => [
                'uuid' => $this->order->uuid,
                'status' => $this->order->status,
            ],
            'redirect' => ($this->isCod) ?
                        config('app.client_url').'/orders/'.$this->order->uuid : route('payment.visit', ['payment' => $payment->receipt]),
        ], 200) : ['success' => true, 'message' => 'order placed successfully', 'payment' => $payment];



    }


    /**
     * @param array|object $newOrder
     * @return Payment|Model
     */
    protected function createAnPendingPayment(array|object $newOrder):Payment|Model
    {
        return $this->order->payment()->create([
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
            'expire_at' => ($this->isCod) ? now()->addMonth() : now()->addMinutes(config('services.defaults.order_cleanup_time_limit')),
            'payment_provider_id' => $this->paymentService->getProviderModel()->id,
            'customer_id' => $this->customer->id,
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
            $totalQuantity = $productModel->pivot->quantity;
            $productAllStock = $productModel->availableStocks()->get();


            foreach($productAllStock as $stock)
            {
                if ($totalQuantity != 0)
                {
                    if($stock->in_stock_quantity >= $totalQuantity)
                    {
                        // Update Product Stock
                        $stock->sold_quantity = $stock->sold_quantity + $totalQuantity;
                        $stock->save();
                        $this->stockBag[] = ['model' => $stock , 'quantity' => $totalQuantity];
                        $totalQuantity = 0;
                    }elseif($stock->in_stock){
                        // Partially Update Stock From Each Stock
                        $this->stockBag[] = ['model' => $stock , 'quantity' => $totalQuantity];
                        $totalQuantity = $totalQuantity - $stock->in_stock_quantity;
                        // Update Product Stock
                        $stock->sold_quantity = $stock->sold_quantity + $stock->in_stock_quantity;
                        $stock->save();

                    }
                }
            }

        }
    }


    protected function makeOrderShipmentWithInvoice(Address $shippingAddress): void
    {

        $AddressGroup = collect($this->stockBag)->groupBy('address_id')->toArray();
        $customShippingProvider = ShippingProvider::firstWhere('code','custom');
        foreach($AddressGroup as $key => $group)
        {
            foreach ($group as $value)
            {
                $orderShipment = $this->order->shipments()->create([
                    'total_quantity' => $value['quantity'],
                    'pickup_address' => empty($key) ? null : $value['model']->address_id,
                    'delivery_address' => $shippingAddress->id,
                    'shipping_provider_id' => ($this->isCod) ? $customShippingProvider->id : null,
                    'cod' => $this->isCod,
                    'status' => OrderShipment::PROCESSING
                ]);

                $orderInvoice = $this->order->invoices()->create([
                    'order_shipment_id' => $orderShipment->id
                ]);

                $orderShipment->invoice_uid = $orderInvoice->id;
                $orderShipment->save();

            }

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
