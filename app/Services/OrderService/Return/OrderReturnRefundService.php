<?php

namespace App\Services\OrderService\Return;

use App\Models\Order\Order;
use App\Models\Order\OrderProduct;
use App\Services\PaymentService\PaymentService;
use App\Services\ShippingService\ShippingService;

class OrderReturnRefundService
{

    protected PaymentService $paymentService;

    protected ShippingService $shippingService;

    protected Order $order;

    protected ?string $error = null;
    protected ?OrderProduct $orderProduct = null;

    public function __construct(Order $order, PaymentService $paymentService, ShippingService $shippingService)
    {
        $this->order = $order;
        $this->paymentService = $paymentService;
        $this->shippingService = $shippingService;

    }



    public function returnOrderProduct(OrderProduct $orderProduct): void
    {
        $this->orderProduct = $orderProduct;
    }


    public function return()
    {
        if (!is_null($this->orderProduct))
        {
            $this->returnSingle();
        }else{
            $this->returnOrder();
        }
    }



    protected function returnOrder()
    {
        foreach ($this->order->orderProducts as $orderProduct)
        {

        }

    }


    protected function returnSingle()
    {

    }










}
