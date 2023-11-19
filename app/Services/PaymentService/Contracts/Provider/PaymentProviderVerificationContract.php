<?php

namespace App\Services\PaymentService\Contracts\Provider;

use App\Models\Payment\Payment;

interface PaymentProviderVerificationContract
{


    /**
     * @param Payment $payment
     * @param array $data
     * @return bool
     */
    public function verifyWith(Payment $payment, array $data): bool;

    public function webhook(string $signature,object|string$response):bool;

    public function webhookCustom(array $data):bool;


}
