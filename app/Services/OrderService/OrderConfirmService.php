<?php

namespace App\Services\OrderService;

use App\Models\Order\Order;
use App\Models\Order\OrderInvoice;
use App\Models\Order\OrderProduct;
use App\Models\Order\OrderShipment;
use App\Models\Payment\Payment;
use App\Models\Product\Product;
use App\Models\Promotion\VoucherCode;
use Illuminate\Database\Eloquent\Model;

class OrderConfirmService
{
    protected ?string $error = null;
    protected Payment $payment;
    protected ?Order $order = null;
    protected array $usedStockBag = [];

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }


    public function getError(): ?string
    {
        return $this->error;
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    protected function discoverOrder(): void
    {
        if (is_null($this->order)) {
            $this->order = $this->payment->order()->first();
        }
        // Load Necessary Relations
        $this->order->loadMissing([
            'orderProducts',
            'orderProducts.product',
            'orderProducts.product.availableStocks',
            'orderProducts.product.availableStocks.addresses'
        ]);
    }

    public function confirmOrder(): void
    {
        // Ensure Order Model
        $this->discoverOrder();

        // Step 1
        $this->processOrderProducts();

        // Step 2
        $this->updateOrderStatus();

        // Step 3
        $this->updatePaymentStatus();

        // Step 4
        $this->updateUsageOfCouponIfPresent();


    }





    /**
     * Step 1
     * @return void
     */
    protected function processOrderProducts(): void
    {

        $this->order->orderProducts->each(function ($orderProduct) {

            // Step 1.1
            $this->getUpdatedProductStockQuantity($orderProduct->product, $orderProduct->quantity);
            if (!empty($this->usedStockBag) && is_null($this->error)) {
                foreach ($this->usedStockBag as $data) {
                    // Step 1.2
                    $newOrderShipment = $this->makeOrderShipment($orderProduct, $data);
                    // Step 1.3
                    $newOrderInvoice = $this->makeOrderInvoice($newOrderShipment, $orderProduct);
                }
            } else {
                $this->error = 'no stock available for ' . $orderProduct->product->name;
            }

        });

    }

    /**
     * Step 1.1
     * @param Product $product
     * @param int $requiredQuantity
     * @return void
     */
    protected function getUpdatedProductStockQuantity(Product $product, int $requiredQuantity): void
    {
        $quantityFulfilled = 0;

        foreach ($product->availableStocks as $productStock) {
            if ($productStock->in_stock_quantity >= $requiredQuantity - $quantityFulfilled) {
                // Deducted Stock Quantity & Update Product Stock
                $quantityToDeduct = $requiredQuantity - $quantityFulfilled;
                $this->usedStockBag[] = [
                    'quantity' => $quantityToDeduct,
                    'model' => $productStock
                ];
                // Update the quantity fulfilled
                $quantityFulfilled += $quantityToDeduct;
                // Break the loop since the required quantity is fulfilled
                break;
            }
        }

        // If Fulfil Order Quantity, Then Stock Will Be Updated
        if ($quantityFulfilled === $requiredQuantity) {
            // Update Stocks
            foreach ($this->usedStockBag as $data) {
                $data['model']->sold_quantity += $data['quantity'];
                $data['model']->save();
            }
        } else {
            $this->error = $product->name . ' out of stock!';
        }

    }

    /**
     * Step 1.2
     * @param OrderProduct $orderProduct
     * @param array $data
     * @return Model|OrderShipment
     */
    protected function makeOrderShipment(OrderProduct $orderProduct, array $data): Model|OrderShipment
    {
        return $orderProduct->shipment()->create([
            'order_id' => $this->order->id,
            'total_quantity' => $data['quantity'],
            'pickup_address' => $data['model']->addresses->first()->id,
            'delivery_address' => $this->order->shipping_address_id,
            'cod' => $this->order->is_cod,
            'status' => OrderShipment::PROCESSING,
        ]);
    }

    /**
     * Step 1.3
     * @param Model|OrderShipment $orderShipment
     * @param OrderProduct $orderProduct
     * @return OrderInvoice
     */
    protected function makeOrderInvoice(Model|OrderShipment $orderShipment, OrderProduct $orderProduct): OrderInvoice
    {
        $newInvoice = $orderShipment->invoice()->create([
            'uuid' => 'INV_' . $this->order->uuid,
            'order_id' => $this->order->id,
            'order_product_id' => $orderProduct->id,
        ]);
        $orderShipment->invoice_uid = $newInvoice->uuid;
        $orderShipment->save();
        return $newInvoice;
    }





    /**
     * Step 2
     * @return void
     */
    private function updateOrderStatus(): void
    {
        $this->order->status == Order::CONFIRM;
        $this->order->payment_success = true;
        $this->order->save();
    }


    /**
     * Step 3
     * @return void
     */
    protected function updatePaymentStatus(): void
    {
        $this->payment->fill([
            'status' => Payment::COMPLETED,
            'verified' => true
        ])->save();
    }


    /**
     * Step 4
     * @return void
     */
    protected function updateUsageOfCouponIfPresent(): void
    {
        if (!is_null($this->order->voucher)) {
            $voucherModel = VoucherCode::firstWhere('code', '=', $this->order->voucher);
            $voucherModel->times_used++;
            $voucherModel->save();
        }
    }


}
