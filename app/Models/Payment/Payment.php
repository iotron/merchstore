<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

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
    ];





}
