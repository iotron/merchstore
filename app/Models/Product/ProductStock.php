<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;


    protected $fillable = [
        'init_quantity',
        'sold_quantity',
        'in_stock',
        'priority',
    ];

    protected static function booted()
    {
//        ProductStock::observe(ProductStockObserver::class);
    }

    //    public function address()
//    {
//        return $this->belongsTo(Address::class, 'vendor_address_id', 'addressable_id', 'vendor');
//    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return bool
     */
    public function inStock(): bool
    {
        return $this->in_stock_quantity > 0;
    }


    public function minStock(int $count)
    {
        return min($this->in_stock_quantity, $count);
    }



}
