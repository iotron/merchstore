<?php

namespace App\Models\Payment;

use App\Models\Order\Order;
use App\Services\Iotron\LaravelPayments\Contracts\Models\PaymentProviderModelContract;
use App\Services\PaymentService\Providers\Custom\CustomPaymentService;
use App\Services\PaymentService\Providers\Razorpay\RazorpayPaymentService;
use App\Services\PaymentService\Providers\Stripe\StripePaymentService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentProvider extends Model implements PaymentProviderModelContract
{
    use HasFactory;

    public const RAZORPAY = 'razorpay';

    public const RAZORPAYX = 'razorpay-x';

    public const STRIPE = 'stripe';

    public const CUSTOM = 'custom';

    public const CODE_OPTIONS = [
        self::CUSTOM => 'Custom',
        self::RAZORPAY => 'Razorpay',
        self::RAZORPAYX => 'Razorpay X',
        self::STRIPE => 'Stripe',
    ];

    public const AVAILABLE_PROVIDERS = [
        CustomPaymentService::class => 'Cash On Delivery Payment Provider',
        RazorpayPaymentService::class => 'Razorpay Payment Provider',
        StripePaymentService::class => 'Stripe Payment Provider',
    ];

    protected $fillable = [
        'name',
        'code',
        'key',
        'secret',
        'webhook',
        'service_provider',
        'is_primary',
        'has_api',
        'status',
        'desc',
    ];

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class, 'payment_provider_id', 'id');
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class, 'payment_provider_id', 'id');
    }
}
