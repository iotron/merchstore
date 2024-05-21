<?php

namespace App\Models\Order;

use App\Helpers\Money\MoneyCast;
use App\Models\Customer\Customer;
use App\Models\Localization\Address;
use App\Models\Payment\Payment;
use App\Models\Payment\Refund;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property $shipping_is_billing;
 * @property $created_at;
 * @property $id;
 * @property $subtotal;
 * @property $discount;
 * @property $tax;
 * @property $total;
 * @property $uuid;
 * @property $voucher;
 * @property $quantity;
 * @property $is_cod;
 * @property $shipping_address_id;
 */
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

    const CONFIRM = 'confirm';

    const REFUNED = 'refunded';

    public const StatusOptions = [
        self::PROCESSING => 'Processing',
        self::PENDING => 'Pending',
        self::PAYMENT_FAILED => 'Payment Failed!',
        self::CONFIRM => 'Confirm',
        self::REVIEW => 'Review',
        self::ACCEPTED => 'Accepted',
        self::READYTOSHIP => 'Ready To Ship',
        self::INTRANSIT => 'In Transit',
        self::COMPLETED => 'Completed',
        self::CANCELLED => 'Cancelled',
        self::REFUNED => 'Refunded',
    ];

    protected $fillable = [
        'uuid',
        'amount',
        'subtotal',
        'discount',
        'tax',
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
        'shipping_address_id',
        'is_cod',
    ];

    protected $casts = [
        'amount' => MoneyCast::class,
        'subtotal' => MoneyCast::class,
        'discount' => MoneyCast::class,
        'tax' => MoneyCast::class,
        'total' => MoneyCast::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id', 'id');
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class, 'order_id', 'id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id', 'id');
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id', 'id');
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(OrderShipment::class, 'order_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(OrderInvoice::class, 'order_id', 'id');
    }
}
