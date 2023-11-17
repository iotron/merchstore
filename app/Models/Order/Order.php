<?php

namespace App\Models\Order;

use App\Models\Customer\Customer;
use App\Models\Payment\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'customer_gstin',
        'shipping_is_billing',
        'billing_address_id',
    ];



    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }


    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id', 'id');
    }







}
