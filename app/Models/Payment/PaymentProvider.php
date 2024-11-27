<?php

namespace App\Models\Payment;

use App\Models\Order\Order;

use App\Services\Iotron\LaravelRazorpay\Support\Contracts\LaravelRazorpayPaymentProviderModelContract;
use App\Services\Iotron\LaravelRazorpay\Support\Traits\HasLaravelRazorpayPaymentProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentProvider extends Model implements LaravelRazorpayPaymentProviderModelContract
{
    use HasFactory,HasLaravelRazorpayPaymentProvider;

    public const RAZORPAY = 'razorpay';
    public const CASH = 'cash';



    protected $fillable = [
        'name',
        'url',
        'key',
        'secret',
        'webhook',
        'status',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];


    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'payment_provider_id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'payment_provider_id', 'id');
    }
}
