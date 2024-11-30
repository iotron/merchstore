<?php

namespace App\Services\OrderService;

use App\Models\Order\Order;
use App\Models\Order\OrderInvoice;
use App\Models\Order\OrderProduct;
use App\Models\Order\OrderShipment;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentProvider;
use Illuminate\Database\Eloquent\Model;

class OrderConfirmService
{
    protected string $provider;
    protected Order $order;
    protected Payment $payment;
    protected array $errors = [];
    /**
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        // Load Necessary Relations
        $this->order->loadMissing([
            'voucher_code',
            'orderProducts',
            'orderProducts.product',
            'orderProducts.product.availableStocks',
            'orderProducts.product.availableStocks.addresses',
        ]);
        if (!$this->order->is_cod)
        {
            $this->order->loadMissing(['payment','payment.provider']);
            $this->payment = $this->order->payment;
            $this->provider = $this->payment->provider->url;
        }else{
            $this->provider = PaymentProvider::CASH;
        }
    }


    public static function make(Order $order): static
    {
        return new static($order);
    }


    public function setError(string $error_text)
    {
        $this->errors [] = $error_text;
    }

    public function getError(): array
    {
        return $this->errors;
    }


    public function validate(bool $sendMail = true): bool
    {

        // Update Product Stocks, Make Shipment And Invoice
        $this->processOrderConfirmation();

        if ($this->makeThisOrderConfirm())
        {
            if (!$this->order->is_cod)
            {
                $this->confirmPayment();
            }
        }
        $this->updateCouponUsage();

        if ($sendMail)
        {

        }

        return $this->order->status == Order::CONFIRM;

    }


    /**
     * Process Order
     * Check Stock
     * Send For Shipment
     * Make Invoice
     * Full Process Related Products
     * @return void
     */
    private function processOrderConfirmation(): void
    {

        $this->order->orderProducts->each(function (OrderProduct $orderProduct) {

            // Update Sold Stock Of Each Ordered Products
            $allowedOrderedProducts = $this->getQualifiedUpdatedOrderedProductStockArray($orderProduct);
            if (empty($allowedOrderedProducts))
            {
                $this->setError('no stock available for '.$orderProduct->product->name);
            }else{
                $groupedStock = collect($allowedOrderedProducts)->groupBy('pickup_address_id');

                foreach ($groupedStock as $pickupAddress_id => $data) {
                    $totalQuantityOfThisPickupAddress = $data->sum(function ($item) {
                        return $item['quantity'];
                    });

                    // Step 1.2
                    $newOrderShipment = $this->makeOrderShipment($orderProduct, $totalQuantityOfThisPickupAddress, $pickupAddress_id);
                    // Step 1.3
                    $newOrderInvoice = $this->makeOrderInvoice($newOrderShipment, $orderProduct);
                }
            }

        });



    }

    private function getQualifiedUpdatedOrderedProductStockArray(OrderProduct $orderProduct): array
    {
        $product = $orderProduct->product;
        $requiredQuantity = $orderProduct->quantity;
        $quantityFulfilled = 0;
        $bag = [];

        foreach ($product->availableStocks as $productStock) {
            if ($productStock->in_stock_quantity >= $requiredQuantity - $quantityFulfilled) {
                // Deducted Stock Quantity & Update Product Stock
                $quantityToDeduct = $requiredQuantity - $quantityFulfilled;

                $bag[] = [
                    'quantity' => $quantityToDeduct,
                    'stock' => $productStock,
                    'pickup_address_id' => $productStock->addresses->first()->id,
                ];
                // Update the quantity fulfilled
                $quantityFulfilled += $quantityToDeduct;
                // Break the loop since the required quantity is fulfilled
                break;
            }
        }

        // If Fulfil Order Quantity, Then Stock Will Be Updated
        if ($quantityFulfilled === $requiredQuantity) {

            foreach ($bag as $data) {
                $data['stock']->sold_quantity += $data['quantity'];
                $data['stock']->save();
            }
        } else {
            $this->setError($product->name.' out of stock!');
        }
        return $bag;
    }


    protected function makeOrderShipment(OrderProduct $orderProduct,int $allowedQuantity,int $pickup_address_id)
    {
        return $orderProduct->shipment()->create([
            'order_id' => $this->order->id,
            'total_quantity' => $allowedQuantity,
            'pickup_address' => $pickup_address_id,
            'delivery_address' => $this->order->shipping_address_id,
            'cod' => $this->order->is_cod,
            'status' => OrderShipment::PROCESSING,
        ]);
    }



    protected function makeOrderInvoice(Model|OrderShipment $orderShipment, OrderProduct $orderProduct): OrderInvoice
    {
        $newInvoice = $orderShipment->invoice()->create([
            'uuid' => 'INV_'.$this->order->uuid,
            'order_id' => $this->order->id,
            'order_product_id' => $orderProduct->id,
        ]);
        $orderShipment->invoice_uid = $newInvoice->uuid;
        $orderShipment->save();

        return $newInvoice;
    }


    /**
     * UPDATE ORDER STATUS
     * make the order confirm
     * @return bool
     */

    private function makeThisOrderConfirm(): bool
    {
        $this->updateOrderStatus();
        $this->updateOrderPaymentChecker();
        return $this->order->status == Order::CONFIRM;
    }

    private function updateOrderStatus(): void
    {
        $this->order->fill(['status' => Order::CONFIRM])->save();
    }

    protected function updateOrderPaymentChecker(): void
    {
        $this->order->fill(['payment_success' => true])->save();
    }

    private function confirmPayment(): void
    {
        $this->payment->fill(['verified' => true])->save();
    }

    private function updateCouponUsage(): void
    {
        if (! is_null($this->order->voucher))
        {
            $this->order->voucher_code->increment('times_used');
        }
    }


}
