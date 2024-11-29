<?php

namespace App\Services\OrderService;

use App\Models\Order\Order;

class OrderConfirmService
{

    protected Order $order;

    /**
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }


    public static function make(Order $order): static
    {
        return new static($order);
    }

    public function validate()
    {
        dd('validation area');
    }
}
