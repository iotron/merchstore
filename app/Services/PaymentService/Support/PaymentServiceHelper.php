<?php

namespace App\Services\PaymentService\Support;

use App\Models\Payment\Payment;
use Illuminate\Support\Str;

class PaymentServiceHelper
{


    // try to not
    public static function newReceipt():?string
    {
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Custom character set
        $prefix = now()->format('dHis'); // Timestamp prefix
        $maxAttempts = 10;
        $attempt = 0;
        do {
            $random = substr(str_shuffle(str_repeat($characters, 4)), 0, 4);
            $random2 = substr(str_shuffle(str_repeat($characters, 4)), 0, 3);
            $id = 'receipt_'.$random2.$prefix . $random;
            $attempt++;
        } while (Payment::where('receipt', $id)->exists() && $attempt < $maxAttempts);

        if ($attempt == $maxAttempts) {
            //throw new Exception('Unable to generate unique ID');
            return null;
        }

        return $id;
    }



}
