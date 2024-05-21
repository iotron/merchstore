<?php

namespace App\Services\OrderService\Backup;

use App\Helpers\Cart\Cart;
use App\Helpers\Money\Money;
use App\Models\Customer\Customer;
use App\Models\Localization\Address;
use App\Models\Order\Order;
use App\Models\Order\OrderProduct;
use App\Models\Order\OrderShipment;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentProvider;
use App\Models\Product\Product;
use App\Models\Shipping\ShippingProvider;
use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\PaymentServiceContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderCreationService
{
    protected Cart $cart;

    protected ?PaymentServiceContract $paymentService;

    protected ?PaymentProviderContract $paymentProvider;

    protected string $receipt;

    protected array $cartMeta = [];

    protected Authenticatable|Customer $customer;

    protected string $provider;

    protected ?Order $order = null;

    public bool $isCod = false;

    protected array $stockBag = [];

    public function __construct(?PaymentProviderContract $paymentProvider, Cart $cart)
    {
        $this->paymentProvider = $paymentProvider;
        $this->cart = $cart;
        $this->cartMeta = $this->cart->getMeta();
        $this->cart->getCustomer()->loadMissing('payments');
        $this->receipt = self::getUniqueReceiptID($this->cart->getCustomer()->payments);
        $this->customer = $this->cart->getCustomer();
        $this->provider = $this->paymentProvider->getClass();
        if (! is_null($this->paymentProvider->getModel())) {
            $this->isCod = $this->paymentProvider->getModel()->code == PaymentProvider::CUSTOM;
        }

    }

    public function isCod(): bool
    {
        return $this->isCod;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function checkout(string $uu_id, Address $shippingAddress, Address $billing_address)
    {

        $uuid = $uu_id;

        // Create An Pending Order Based On Newly Created Payment
        $this->order = $this->customer->orders()->create([
            'uuid' => $uuid,
            'voucher' => $this->cartMeta['coupon'] ?? '',
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
            'shipping_is_billing' => $shippingAddress->id == $billing_address->id,
            'billing_address_id' => $billing_address->id,
            'shipping_address_id' => $shippingAddress->id,
            'is_cod' => $this->isCod,
        ]);

        // Now Make AN Order On Payment Provider Based On This Order
        $newOrder = $this->paymentProvider->order()->create($this->order);
        // Create An Pending Payment Based On Provider New Order Data (DB Case)
        $payment = $this->createAnPendingPayment($newOrder);

        // Left Job
        $this->process($shippingAddress);

        // Finally Update Order Quantity From Its Order Product Quantities
        $allQuantityUsed = $this->order->orderProducts->sum('quantity');
        if ($this->order->quantity != $allQuantityUsed) {
            $this->order->quantity = $allQuantityUsed;
            $this->order->save();
        }

        //
        //        if($this->isCod)
        //        {
        //            // Update Product Stock
        //            $this->updateProductStock();
        //        }
        //
        //        // Attaching Products To Order
        //        $this->attachProductsToOrder();
        //
        //        // Create Shipment  & Invoice Of ThisOrder
        //
        //        $this->makeOrderShipmentWithInvoice($shippingAddress);
        //

        // Clean Up Cart
        $this->cart->reset();

    }

    protected function createAnPendingPayment(array|object $newOrder): Payment|Model
    {

        return $this->order->payment()->create([
            'receipt' => $this->receipt,
            'provider_gen_id' => $newOrder['id'],
            'provider_class' => $this->provider,
            'voucher' => $this->cartMeta['coupon'] ?? '',
            'quantity' => $this->cartMeta['quantity'],
            'subtotal' => ($this->cartMeta['subtotal'] instanceof Money) ? $this->cartMeta['subtotal']->getAmount() : $this->cartMeta['subtotal'],
            'discount' => ($this->cartMeta['discount'] instanceof Money) ? $this->cartMeta['discount']->getAmount() : $this->cartMeta['discount'],
            'tax' => ($this->cartMeta['tax'] instanceof Money) ? $this->cartMeta['tax']->getAmount() : $this->cartMeta['tax'],
            'total' => ($this->cartMeta['total'] instanceof Money) ? $this->cartMeta['total']->getAmount() : $this->cartMeta['total'],
            'details' => is_object($newOrder) ? $newOrder->toArray() : $newOrder,
            'expire_at' => ($this->isCod) ? now()->addMonth() : now()->addMinutes(config('services.defaults.order_cleanup_time_limit')),
            'payment_provider_id' => $this->paymentProvider->getModel()->id,
            'customer_id' => $this->customer->id,
        ]);
    }

    protected function process(Address $shippingAddress): void
    {
        foreach ($this->cartMeta['products'] as $item) {
            $orderProduct = $this->attachIntoOrderProduct($item);
            $product = $item['product'];
            $product->loadMissing('availableStocks');
            // If Cod We Update Product Stock
            if ($this->isCod) {
                $this->updateStock($product, $shippingAddress, $orderProduct);
            } else {
                // If Not Cod We Just Make Shipment And Invoice
                $this->makeShipmentAndInvoice($shippingAddress, $orderProduct, $product, null, null);
            }
            // Finally
            // Update Quantity Of Order Product As Per Stock Use In Shipment
            $usedQuantity = $orderProduct->shipment->sum('total_quantity');
            if ($orderProduct->quantity != $usedQuantity) {
                $orderProduct->quantity = $usedQuantity;
                $orderProduct->save();
            }

        }

    }

    protected function attachIntoOrderProduct(array $item): Model
    {
        $discountAmount = isset($item['total_discount_amount']) ? $item['total_discount_amount'] : new Money(0.0);

        $records = [
            'quantity' => $item['pivot_quantity'],
            'amount' => ($item['total_base_amount'] instanceof Money) ? $item['total_base_amount']->getAmount() : $item['total_base_amount'],
            'discount' => ($discountAmount instanceof Money) ? $discountAmount->getAmount() : $discountAmount,
            'tax' => ($item['total_tax_amount'] instanceof Money) ? $item['total_tax_amount']->getAmount() : $item['total_tax_amount'],
            'total' => ($item['net_total'] instanceof Money) ? $item['net_total']->getAmount() : $item['net_total'],
            'product_id' => $item['id'],
        ];

        return $this->order->orderProducts()->create($records);
    }

    protected function updateStock(Product $product, Address $shippingAddress, OrderProduct|Model $orderProduct)
    {
        $requiredQuantity = $product->pivot->quantity;
        $quantityFulfilled = 0;

        foreach ($product->availableStocks as $stock) {
            // dump($quantityFulfilled);
            if ($stock->in_stock_quantity >= $requiredQuantity - $quantityFulfilled) {
                // Deducted Stock Quantity & Update Product Stock
                $quantityToDeduct = $requiredQuantity - $quantityFulfilled;

                $stock->sold_quantity += $quantityToDeduct;
                $stock->save();

                $pickUpAddress = $stock->addresses()->first();

                $this->makeShipmentAndInvoice($shippingAddress, $orderProduct, $product, $pickUpAddress, $quantityToDeduct);

                // Update the quantity fulfilled
                $quantityFulfilled += $quantityToDeduct;

                // Break the loop since the required quantity is fulfilled
                break;
            } else {
                $pickUpAddress = $stock->addresses()->first();
                $quantityToDeduct = $stock->in_stock_quantity;

                $stock->sold_quantity += $quantityToDeduct;
                $stock->save();

                $this->makeShipmentAndInvoice($shippingAddress, $orderProduct, $product, $pickUpAddress, $quantityToDeduct);

                // Update the quantity fulfilled
                $quantityFulfilled += $quantityToDeduct;
            }

        }
        // dd($quantityFulfilled,$requiredQuantity);
    }

    protected function makeShipmentAndInvoice(Address $shippingAddress, OrderProduct|Model $orderProduct, Product $product, ?Address $pickupAddress, ?int $quantity = null): void
    {

        if (! is_null($pickupAddress) && $this->isCod && ! is_null($quantity)) {
            // Cod Case
            $orderShipment = $this->newShipmentCreation($quantity, $shippingAddress, $pickupAddress, $orderProduct, $product);
        } else {
            // Normal Case
            $requiredQuantity = $product->pivot->quantity;
            $quantityFulfilled = 0;

            foreach ($product->availableStocks as $stock) {
                if ($stock->in_stock_quantity >= $requiredQuantity - $quantityFulfilled) {
                    // Deducted Stock Quantity & Update Product Stock
                    $quantityToDeduct = $requiredQuantity - $quantityFulfilled;
                    $pickUpAddress = $stock->addresses()->first();
                    $orderShipment = $this->newShipmentCreation($quantityToDeduct, $shippingAddress, $pickUpAddress, $orderProduct, $product);
                    // Update the quantity fulfilled
                    $quantityFulfilled += $quantityToDeduct;
                    break;
                } else {
                    $pickUpAddress = $stock->addresses()->first();
                    $quantityToDeduct = $stock->in_stock_quantity;
                    $orderShipment = $this->newShipmentCreation($quantityToDeduct, $shippingAddress, $pickUpAddress, $orderProduct, $product);
                    // Update the quantity fulfilled
                    $quantityFulfilled += $quantityToDeduct;
                }
            }
        }
    }

    protected function newShipmentCreation(int $quantity, Address $shippingAddress, Address $pickupAddress, OrderProduct $orderProduct, Product $product): Model
    {
        $customShippingProvider = ShippingProvider::firstWhere('code', 'custom');

        $orderShipment = $orderProduct->shipment()->create([
            'order_id' => $this->order->id,
            'total_quantity' => $quantity,
            'pickup_address' => $pickupAddress->id,
            'delivery_address' => $shippingAddress->id,
            'shipping_provider_id' => ($this->isCod) ? $customShippingProvider->id : null,
            'cod' => $this->isCod,
            'status' => OrderShipment::PROCESSING,
        ]);

        $orderInvoice = $orderShipment->invoice()->create([
            'order_id' => $this->order->id,
        ]);

        $orderShipment->invoice_uid = $orderInvoice->id;
        $orderShipment->save();

        return $orderShipment;
    }

    //
    //
    //    protected function attachProductsToOrder(): void
    //    {
    //            $records = [];
    //            foreach ($this->cartMeta['products'] as $item)
    //            {
    //               $discountAmnt = isset($item['total_discount_amount']) ? $item['total_discount_amount'] : new Money(0.0);
    //
    //
    //                $records [] = [
    //                    'quantity' => $item['pivot_quantity'],
    //                    'amount' => ($item['total_base_amount'] instanceof  Money) ? $item['total_base_amount']->getAmount() : $item['total_base_amount'],
    //                    'discount' => ($discountAmnt instanceof  Money) ? $discountAmnt->getAmount() : $discountAmnt,
    //                    'tax' => ($item['total_tax_amount'] instanceof  Money) ? $item['total_tax_amount']->getAmount() : $item['total_tax_amount'],
    //                    'total' => ($item['net_total'] instanceof  Money) ? $item['net_total']->getAmount() : $item['net_total'],
    //                    'product_id' => $item['id']
    //                ];
    //            }
    //
    //            $this->order->orderProducts()->createMany($records);
    //    }
    //
    //
    //    protected function updateProductStock()
    //    {
    //
    //
    //        foreach ($this->cartMeta['products'] as $item)
    //        {
    //            $productModel = $item['product'];
    //            $totalQuantity = $productModel->pivot->quantity;
    //            $productAllStock = $productModel->availableStocks()->get();
    //
    //
    //
    //            foreach($productAllStock as $stock)
    //            {
    //                if ($totalQuantity != 0)
    //                {
    //                    if($stock->in_stock_quantity >= $totalQuantity)
    //                    {
    //                        // Update Product Stock
    //                        $stock->sold_quantity = $stock->sold_quantity + $totalQuantity;
    //                        $stock->save();
    //                        $this->stockBag[] = ['model' => $stock , 'quantity' => $totalQuantity];
    //                        $totalQuantity = 0;
    //
    //
    //                    }elseif($stock->in_stock){
    //                        // Partially Update Stock From Each Stock
    //                        $this->stockBag[] = ['model' => $stock , 'quantity' => $totalQuantity];
    //                        $totalQuantity = $totalQuantity - $stock->in_stock_quantity;
    //                        // Update Product Stock
    //                        $stock->sold_quantity = $stock->sold_quantity + $stock->in_stock_quantity;
    //                        $stock->save();
    //
    //                    }
    //                }
    //            }
    //
    //        }
    //    }
    //
    //
    //    protected function makeOrderShipmentWithInvoice(Address $shippingAddress): void
    //    {
    //
    //        $AddressGroup = collect($this->stockBag)->groupBy('address_id')->toArray();
    //
    //        $customShippingProvider = ShippingProvider::firstWhere('code','custom');
    //
    //        $orderProducts = $this->order->orderProducts;
    //        foreach($AddressGroup as $key => $group)
    //        {
    //            foreach ($group as $value)
    //            {
    //                foreach ($orderProducts as $item)
    //                {
    //                    $stockAddress = $value['model']->addresses()->first();
    //
    //                    $orderShipment = $item->shipment()->create([
    //                        'order_id' => $this->order->id,
    //                        'total_quantity' => $value['quantity'],
    //                        'pickup_address' => $stockAddress->id,
    //                        'delivery_address' => $shippingAddress->id,
    //                        'shipping_provider_id' => ($this->isCod) ? $customShippingProvider->id : null,
    //                        'cod' => $this->isCod,
    //                        'status' => OrderShipment::PROCESSING,
    //                    ]);
    //
    //
    //                    $orderInvoice = $orderShipment->invoice()->create([
    //                        'order_id' => $this->order->id
    //                    ]);
    //
    //                    $orderShipment->invoice_uid = $orderInvoice->id;
    //                    $orderShipment->save();
    //                }
    //
    //            }
    //        }
    //    }

    protected static function getUniqueReceiptID(object $collection): string
    {
        $uid = 'receipt_'.ucwords(Str::random(6));
        $result = $collection->contains('receipt', $uid);

        return (! $result) ? $uid : self::getUniqueReceiptID($collection);
    }

    protected function getProductArray()
    {
        return collect($this->cartMeta['products'])->keyBy('id')->map(function ($item) {
            return ['quantity' => $item['pivot_quantity']];
        })->toArray();
    }

    //    protected function generateUniqueID() {
    //        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Custom character set
    //        $prefix = now()->format('dHis'); // Timestamp prefix
    //        $maxAttempts = 10;
    //        $attempt = 0;
    //
    //        do {
    //            $random = substr(str_shuffle(str_repeat($characters, 4)), 0, 4);
    //            $id = $prefix . $random;
    //            $attempt++;
    //        } while (Order::where('uuid', $id)->exists() && $attempt < $maxAttempts);
    //
    //        if ($attempt == $maxAttempts) {
    //            //throw new Exception('Unable to generate unique ID');
    //            return null;
    //        }
    //
    //        return $id;
    //    }

}
