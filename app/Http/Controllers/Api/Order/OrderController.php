<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderIndexResource;
use Illuminate\Http\Request;

class OrderController extends Controller
{




    public function index()
    {
        $customer = auth('customer')->user();
        return OrderIndexResource::collection($customer->orders);
    }




}
