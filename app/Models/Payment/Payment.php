<?php

namespace App\Models\Payment;

use App\Casts\MoneyCast;
use App\Models\Customer\Customer;
use App\Models\Order\Order;
use App\Services\Iotron\LaravelRazorpay\Support\Cast\PaymentModelTypeCast;
use App\Services\Iotron\LaravelRazorpay\Support\Contracts\LaravelRazorpayPaymentModelContract;
use App\Services\Iotron\LaravelRazorpay\Support\Traits\HasLaravelRazorpayPayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property $payment_provider_id
 */
class Payment extends Model implements LaravelRazorpayPaymentModelContract
{
    use HasFactory,HasLaravelRazorpayPayment;

    // New Fillable
    protected $fillable = [

        'provider_gen_id',
        'provider_transaction_id',
        'provider_generated_sign',
        'amount',
        'type',
        'bookable_type',
        'bookable_id',
        'provider_gen_url',
        'callback_url',
        'success_url',
        'failure_url',
        'expire_at',
        'verified',
//        'status',
        'payment_provider_id',
        'details',
    ];



    protected $casts = [
        'details' => 'array',
        'verified' => 'boolean',
        'type' => PaymentModelTypeCast::class,
//        'status' => PaymentModelStatusCast::class,
        'amount' => MoneyCast::class,
    ];

    protected $allowedFilters = [
        //        'provider_gen_id',
        //        'provider_ref_id',
        'order_id',
        'transaction_id',
    ];


    public function bookable(): MorphTo
    {
        return $this->morphTo();
    }

//    public function customer(): BelongsTo
//    {
//        return $this->belongsTo(Customer::class, 'customer_id', 'id');
//    }

//    public function order(): BelongsTo
//    {
//        return $this->belongsTo(Order::class, 'order_id', 'id');
//    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(PaymentProvider::class, 'payment_provider_id', 'id');
    }

    public function refunds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Refund::class, 'payment_id', 'id');
    }
}
