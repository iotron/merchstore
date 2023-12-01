<?php

namespace App\Models\Order;

use App\Helpers\Money\MoneyCast;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProduct extends Model
{
    use HasFactory;


//    public const PROCESSING = 'processing';
//    public const SHIPPED = 'shipped';
//    public const RETURNED = 'returned';
//    public const RESTOCKED_AFTER_RETURN = 'restock_after_return';
//
//    public const STATUS_OPTIONS = [
//        self::PROCESSING => 'Processing',
//        self::SHIPPED => 'Shipped',
//        self::RETURNED => 'Returned',
//        self::RESTOCKED_AFTER_RETURN => 'Restock',
//    ];




    protected $fillable = [
        'quantity',
        'amount',
        'discount',
        'tax',
        'total',
        'has_tax',
        'product_id',
        'status',
        'status_feedback'
    ];

    protected $casts = [
        'amount' => MoneyCast::class,
        'subtotal' => MoneyCast::class,
        'discount' => MoneyCast::class,
        'tax' => MoneyCast::class,
        'total' => MoneyCast::class,
    ];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }


    public function shipment()
    {
        return $this->belongsToMany(OrderShipment::class, 'shipment_products','order_product_id');
    }


}
