<?php

namespace App\Services\OrderService\Backup;

use App\Models\Order\Order;
use App\Models\Order\OrderProduct;
use App\Models\Order\OrderShipment;
use App\Models\Payment\Payment;
use App\Models\Product\Product;
use App\Models\Promotion\VoucherCode;
use Illuminate\Database\Eloquent\Model;

class OrderConfirmService
{


    protected Payment $payment;
    protected Order $order;
    protected ?VoucherCode $voucherCode=null;
    protected array $stockBag = [];
    protected bool $isCod = false;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
//        throw_if($this->payment->provider->url == PaymentProvider::COD,'cash on delivery order not submit with this service');
        $this->order = $this->payment->order;
        $this->order->loadMissing('orderProducts','orderProducts.product');
        $this->voucherCode = VoucherCode::firstWhere('code',$this->order->voucher);

    }

    public function getOrder()
    {
        return $this->order;
    }

    public function updateOrder():bool
    {
        $this->updateOrderStatus();
        $this->updatePaymentStatus();
        $this->updateProductStock();
        $this->updateUsageOfCouponIfPresent();

        // Left Jobs
        // Return
        return $this->order->status == Order::CONFIRM;
    }

    private function updateOrderStatus(): void
    {
        $this->order->status == Order::CONFIRM;
        $this->order->payment_success = true;
        $this->order->save();
    }


    protected function updatePaymentStatus(): void
    {
        $this->payment->fill([
            'status' => Payment::COMPLETED,
            'verified' => true
        ])->save();
    }

//    private function updateProductStock():void
//    {
//
//
//        foreach ($this->order->orderProducts as $orderProduct)
//        {
//            $productModel = $orderProduct->product;
//
//            $totalQuantity = $orderProduct->quantity;
//            $productAllStock = $productModel->availableStocks()->get();
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
//                    }elseif($stock->in_stock){
//                        // Partially Update Stock From Each Stock
//                        $this->stockBag[] = ['model' => $stock , 'quantity' => $totalQuantity];
//                        $totalQuantity = $totalQuantity - $stock->in_stock_quantity;
//                        // Update Product Stock
//                        $stock->sold_quantity = $stock->sold_quantity + $stock->in_stock_quantity;
//                        $stock->save();
//                    }
//                }
//            }
//
//        }
//    }
//
//






    protected function updateProductStock()
    {
        $this->order->loadMissing('orderProducts','orderProducts.product','orderProducts.product.availableStocks','orderProducts.shipment');

        foreach ($this->order->orderProducts as $orderProduct)
        {

            foreach ($orderProduct->shipment as $orderShipment)
            {

                $this->updateStock($orderProduct->product,$orderShipment,$orderProduct);
            }


        }

    }





    protected function updateStock(Product $product, OrderShipment $orderShipment, OrderProduct|Model $orderProduct)
    {

       // dd($product);
        $requiredQuantity = $this->order->quantity;
        $quantityFulfilled = 0;

        foreach ($product->availableStocks as $stock) {

            if ($stock->address_id == $orderShipment->pickup_address)
            {
                if ($stock->in_stock_quantity >= $requiredQuantity - $orderShipment->total_quantity) {
                    // Deducted Stock Quantity & Update Product Stock
                    $quantityToDeduct = $requiredQuantity - $orderShipment->total_quantity;

                    $stock->sold_quantity += $quantityToDeduct;
                    $stock->save();

                    $pickUpAddress = $stock->addresses()->first();

                    // Update the quantity fulfilled
                    $quantityFulfilled += $quantityToDeduct;

                    // Break the loop since the required quantity is fulfilled
                    break;
                }
            }

        }
        // dd($quantityFulfilled,$requiredQuantity);
    }





    private function updateUsageOfCouponIfPresent(): void
    {
        if (!is_null($this->voucherCode))
        {
            $this->voucherCode->times_used++;
            $this->voucherCode->save();
        }

    }






}
