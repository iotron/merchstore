<?php

namespace App\Services\OrderService\Supports;

class OrderUpdateHandler
{



    public function updateOrder(): void
    {
        $this->updateOrderStatus();
        $this->updateProductStock();
        $this->updateUsageOfCouponIfPresent();
        $this->generateOrderShipmentWithInvoice();
    }

    private function updateOrderStatus()
    {
    }

    private function updateProductStock()
    {
    }

    private function updateUsageOfCouponIfPresent()
    {
    }

    private function generateOrderShipmentWithInvoice()
    {

    }


}
