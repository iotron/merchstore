<?php

namespace App\Models\Payment;

use App\Helpers\Money\MoneyCast;
use App\Models\Customer\Customer;
use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const PAYMENT_FAILED = 'payment_failed';
    const COMPLETED = 'completed';
    const REFUND = 'refund';
    const CANCEL_REFUND = 'cancel_refund';


    public const STATUS_OPTION = [
        self::PENDING => 'Pending',
        self::CANCEL_REFUND => 'Cancel Payment',
        self::PROCESSING => 'Processing',
        self::COMPLETED => 'Completed',
        self::PAYMENT_FAILED => 'Payment Failed',
        self::REFUND => 'Refund'
    ];


    protected $fillable = [
        'receipt',
        'provider_gen_id',
        'provider_ref_id',
        'provider_gen_sign',
        'provider_class',
        'voucher',
        'quantity',
        'subtotal',
        'discount',
        'tax',
        'total',
        'details',
        'error',
        'verified',
        'status',
        'expire_at',
        'payment_provider_id',
        'customer_id'
    ];


    protected $casts = [
        'details' => 'array',
        'subtotal' => MoneyCast::class,
        'discount' => MoneyCast::class,
        'tax' => MoneyCast::class,
        'total' => MoneyCast::class,
    ];


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class,'customer_id','id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id','id');
    }


    public function provider(): BelongsTo
    {
        return $this->belongsTo(PaymentProvider::class,'payment_provider_id','id');
    }



}
