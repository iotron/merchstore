<?php

namespace App\Services\PaymentService\Contracts\Provider;

use App\Models\Order\Order;

interface PaymentProviderOrderContract
{

    public function create(Order $order):array;

    public function fetch(string|int $id);

    public function all();

}
