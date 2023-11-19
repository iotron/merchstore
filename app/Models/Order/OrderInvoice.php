<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order\Order;
use App\Models\Order\OrderShipment;

class OrderInvoice extends Model
{
    use HasFactory;

    protected $fillable=[
        'order_id',
        'order_shipment_id'
    ];



    public function order()
    {
        return $this->belongsTo(Order::class,'order_id','id');
    }

    public function shipment()
    {
        return $this->belongsTo(OrderShipment::class);
    }



}
