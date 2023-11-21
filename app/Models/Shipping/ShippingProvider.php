<?php

namespace App\Models\Shipping;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingProvider extends Model
{
    use HasFactory;

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






}
