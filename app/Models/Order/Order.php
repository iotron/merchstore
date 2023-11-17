<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    protected $fillable = [
        'order_receipt',
        'amount',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total',
        'quantity',
        'voucher',
        'tracking_id',
        'status',
        'payment_success',
        'expire_at',
        'customer_id',
        'payment_provider_id',
        'customer_gstin',
        'shipping_is_billing',
        'billing_address_id',
        'address_id',
    ];



}
