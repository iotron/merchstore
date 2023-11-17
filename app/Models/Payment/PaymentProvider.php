<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentProvider extends Model
{
    use HasFactory;

    public const RAZORPAY = 'razorpay';
    public const RAZORPAYX = 'razorpay-x';
    public const STRIPE = 'stripe';


    protected $fillable = [
        'name',
        'url',
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
        return $this->hasMany(Payment::class,'payment_provider_id','id');
    }


}
