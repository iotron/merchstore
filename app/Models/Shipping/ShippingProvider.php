<?php

namespace App\Models\Shipping;

use App\Services\ShippingService\Providers\Custom\CustomShippingService;
use App\Services\ShippingService\Providers\Pickrr\PickrrShippingService;
use App\Services\ShippingService\Providers\ShipRocket\ShipRocketShippingService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingProvider extends Model
{
    use HasFactory;

    public const CUSTOM = 'custom';
    public const SHIPROCKET = 'shiprocket';


    public const AVAILABLE_PROVIDERS = [
        CustomShippingService::class => 'Custom Shipping Provider (Cash On Delivery)',
        ShipRocketShippingService::class => 'ShipRocket Shipping Provider',
        PickrrShippingService::class => 'Pickrr Shipping Provider'
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






}
