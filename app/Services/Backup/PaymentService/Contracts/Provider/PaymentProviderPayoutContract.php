<?php

namespace App\Services\PaymentService\Contracts\Provider;

use App\Models\Transaction\Payout;
use Illuminate\Database\Eloquent\Model;

interface PaymentProviderPayoutContract
{
    public function toBank(Payout|Model $payout);
}
