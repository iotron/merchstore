<?php

namespace App\Models\Order;

use App\Models\Customer\Customer;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    // order status
    const PROCESSING = 'processing';
    const PENDING = 'pending';
    const PAYMENT_FAILED = 'payment_failed';
    public const REVIEW = 'review';
    public const ACCEPTED = 'accepted';
    public const READYTOSHIP = 'readytoship';
    public const INTRANSIT = 'intransit';
    const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    public const StatusOptions = [
        self::PROCESSING => 'Processing',
        self::REVIEW => 'Review',
        self::ACCEPTED => 'Accepted',
        self::READYTOSHIP => 'Ready To Ship',
        self::INTRANSIT => 'In Transit',
        self::COMPLETED => 'Completed',
        self::CANCELLED => 'Cancelled'
    ];


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
        return $this->belongsTo(Customer::class,'customer_id','id');
    }


    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id', 'id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentProvider::class, 'payment_provider_id');
    }





}
