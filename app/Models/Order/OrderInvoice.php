<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'order_id',
        'order_product_id',
        'order_shipment_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class, 'order_product_id', 'id');
    }

    public function shipment()
    {
        return $this->belongsTo(OrderShipment::class, 'order_shipment_id', 'id');
    }
}
