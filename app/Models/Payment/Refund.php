<?php

namespace App\Models\Payment;

use App\Helpers\Money\MoneyCast;
use App\Models\Order\Order;
use App\Models\Order\OrderProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    use HasFactory;

    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const FAILED = 'payment_failed';
    const COMPLETED = 'completed';
    const CANCEL = 'cancel';


    public const STATUS_OPTION = [
        self::PENDING => 'Pending',
        self::CANCEL => 'Cancel Payment',
        self::PROCESSING => 'Processing',
        self::COMPLETED => 'Completed',
        self::FAILED => 'Payment Failed',
    ];


    protected $fillable = [
        'refund_id',
        'amount',
        'currency',
        'payment_id',
        'receipt',
        'speed',
        'status',
        'batch_id',
        'notes',
        'tracking_data',
        'details',
        'error',
        'order_id',
        'verified',
		'provider_payment_id'
    ];


    protected $casts = [
        'notes' => 'array',
        'tracking_data' => 'array',
        'details' => 'array',
        'error' => 'array',
        'verified' => 'bool',
        'amount' => MoneyCast::class
    ];


    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class,'payment_id','id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class,'order_id','id');
    }

    public function orderProduct(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class,'order_product_id','id');
    }






}
