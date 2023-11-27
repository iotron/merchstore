<?php

namespace App\Services\PaymentService\Providers\Razorpay\Support;

use App\Models\Order\Order;
use App\Services\PaymentService\Support\PaymentServiceHelper;

class RazorpayOrderFormatter
{




    public static function getArray(Order $order): array
    {
        return [
            'receipt' => PaymentServiceHelper::generateUniquePaymentReceipt(),
            'amount' => $order->total->getAmount(),
            'currency' => $order->total->getCurrency()->getCurrency(),

//            'notes' => [
//                'booking_name' => $order->,
//                'booking_email' => $this->bookingEmail,
//                'booking_contact' => $this->bookingContact,
//                'model_id' => !is_null($this->subjectModel) ? $this->subjectModel->id : null,
//                'products_ids' => implode(',', $this->cartMeta['products']->pluck('id')->toArray()),
//                'product_details' => json_encode($this->items),
//                // Currently Not Necessary (Remove When Update Livewire)
//                'voucher' => $this->cartMeta['coupon'] ?? '',
//                // column
//                'quantity' => $this->cartMeta['quantity'],
//                'subtotal' => $this->cartMeta['subtotal']->getAmount(),
//                'discount' => $this->cartMeta['discount']->getAmount(),
//                'tax' => $this->cartMeta['tax']->getAmount(),
//                'total' => $this->cartMeta['total']->getAmount(),
//            ],
        ];
    }







}
