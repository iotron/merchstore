<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderIndexResource;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{




    public function index()
    {
        $customer = auth('customer')->user();
        $allOrders = $customer->orders()->paginate();
        return OrderIndexResource::collection($allOrders);
    }


    public function show(Order $order)
    {
        // Order Policy customer individual check

        $order->load('billingAddress','invoices','payment','shipments','orderProducts');
        return OrderResource::make($order);
    }



}
