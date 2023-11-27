<?php

namespace App\Services\OrderService;

use App\Helpers\Cart\Cart;
use App\Helpers\Money\Money;
use App\Models\Customer\Customer;
use App\Models\Localization\Address;
use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentProvider;
use App\Models\Product\Product;
use App\Services\PaymentService\Contracts\PaymentServiceContract;
use App\Services\OrderService\Supports\Order\OrderBuilder;
use Exception;
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
    public bool $isCod = false;
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


    public function isCod ():bool
    {
        return $this->isCod;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }





    public function checkout(string $uu_id,Address $shippingAddress,Address $billing_address): \Illuminate\Http\JsonResponse|array
    {


        $uuid = $uu_id;

        // Create An Pending Order Based On Newly Created Payment
        $this->order =  $this->customer->orders()->create([
            'uuid' => $uuid,
            'voucher' => $this->cartMeta['coupon'] ?? '',
            'quantity' => $this->cartMeta['quantity'],
            'amount' => $this->cartMeta['total'],
            'subtotal' => $this->cartMeta['subtotal'],
            'discount' => $this->cartMeta['discount'],
            'tax' => $this->cartMeta['tax'],
            'total' => $this->cartMeta['total'],
            'status' => (!$this->isCod) ? Order::PENDING : Order::CONFIRM,
            'payment_success' => false,
            'expire_at' => ($this->isCod) ? now()->addMonth() : now()->addMinutes(config('services.defaults.order_cleanup_time_limit')),
            'customer_id' => $this->customer->id,
            'customer_gstin' => null, // need data here
            'shipping_is_billing' => false,
            'billing_address_id' => $billing_address->id,
            'shipping_address_id' => $shippingAddress->id
        ]);






//        // Prepare An Order Array (Provider Case) (code shorten)
//        $orderBuilder = new OrderBuilder($this->paymentService);
//        $orderArray = $orderBuilder
//            ->model(null)
//            ->receipt($this->receipt)
//            ->items($this->getProductArray())
//            ->cartMeta($this->cartMeta)
//            ->bookingName($this->customer->name)
//            ->bookingEmail($this->customer->email)
//            ->bookingContact($this->customer->contact)
//            ->getArray();

        // Create New Order Via Payment Provider Based On Order Array (Provider Case)
        $newOrder = $this->paymentService->provider()->order()->create($this->order);



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
            'subtotal' => ($this->cartMeta['subtotal'] instanceof  Money) ? $this->cartMeta['subtotal']->getAmount() : $this->cartMeta['subtotal'],
            'discount' => ($this->cartMeta['discount'] instanceof  Money) ? $this->cartMeta['discount']->getAmount() : $this->cartMeta['discount'],
            'tax' => ($this->cartMeta['tax'] instanceof Money) ? $this->cartMeta['tax']->getAmount() : $this->cartMeta['tax'],
            'total' => ($this->cartMeta['total'] instanceof Money) ? $this->cartMeta['total']->getAmount() : $this->cartMeta['total'],
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
               $discountAmnt = isset($item['total_discount_amount']) ? $item['total_discount_amount'] : new Money(0.0);


                $records [] = [
                    'quantity' => $item['pivot_quantity'],
                    'amount' => ($item['total_base_amount'] instanceof  Money) ? $item['total_base_amount']->getAmount() : $item['total_base_amount'],
                    'discount' => ($discountAmnt instanceof  Money) ? $discountAmnt->getAmount() : $discountAmnt,
                    'tax' => ($item['total_tax_amount'] instanceof  Money) ? $item['total_tax_amount']->getAmount() : $item['total_tax_amount'],
                    'total' => ($item['net_total'] instanceof  Money) ? $item['net_total']->getAmount() : $item['net_total'],
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
        $orderProducts = $this->order->orderProducts;
        foreach($AddressGroup as $key => $group)
        {
            foreach ($group as $value)
            {
                foreach ($orderProducts as $item)
                {

//                    $orderShipment = $item->shipment()->create([
//                        'total_quantity' => $value['quantity'],
//                        'pickup_address' => empty($key) ? null : $value['model']->address_id,
//                        'delivery_address' => $shippingAddress->id,
//                        'shipping_provider_id' => ($this->isCod) ? $customShippingProvider->id : null,
//                        'cod' => $this->isCod,
//                        'status' => OrderShipment::PROCESSING,
//                    ]);


                    $orderShipment = $item->shipment()->create([
                        'order_id' => $this->order->id,
                        'total_quantity' => $value['quantity'],
                        'pickup_address' => empty($key) ? null : $value['model']->address_id,
                        'delivery_address' => $shippingAddress->id,
                        'shipping_provider_id' => ($this->isCod) ? $customShippingProvider->id : null,
                        'cod' => $this->isCod,
                        'status' => OrderShipment::PROCESSING,
                    ]);



                    $orderInvoice = $orderShipment->invoice()->create([
                        'order_id' => $this->order->id
                    ]);

                    $orderShipment->invoice_uid = $orderInvoice->id;
                    $orderShipment->save();


                }



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
        return collect($this->cartMeta['products'])->keyBy('id')->map(function ($item) {
            return ['quantity' => $item['pivot_quantity']];
        })->toArray();
    }



    protected function generateUniqueID() {
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Custom character set
        $prefix = now()->format('dHis'); // Timestamp prefix
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $random = substr(str_shuffle(str_repeat($characters, 4)), 0, 4);
            $id = $prefix . $random;
            $attempt++;
        } while (Order::where('uuid', $id)->exists() && $attempt < $maxAttempts);

        if ($attempt == $maxAttempts) {
            //throw new Exception('Unable to generate unique ID');
            return null;
        }

        return $id;
    }



}
