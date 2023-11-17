<?php

namespace App\Services\PaymentService\Contracts\Provider;

use App\Models\Customer\Booking;

interface PaymentProviderRefundContract
{


    public function create(Booking $booking);

}
