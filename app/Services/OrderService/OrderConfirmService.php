<?php

namespace App\Services\OrderService;

use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentProvider;
use App\Models\Promotion\Voucher;
use App\Models\Promotion\VoucherCode;

class OrderConfirmService
{


    protected Payment $payment;
    protected Order $order;
    protected ?VoucherCode $voucherCode=null;
    protected array $stockBag = [];

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

    private function updateProductStock():void
    {


        foreach ($this->order->orderProducts as $orderProduct)
        {
            $productModel = $orderProduct->product;

            $totalQuantity = $orderProduct->quantity;
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

    private function updateUsageOfCouponIfPresent(): void
    {
        if (!is_null($this->voucherCode))
        {
            $this->voucherCode->times_used++;
            $this->voucherCode->save();
        }

    }


}
