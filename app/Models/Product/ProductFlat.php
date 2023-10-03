<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFlat extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'short_description',
        'meta_data',
        'width',
        'height',
        'length',
        'weight',
    ];

    protected $casts = [
        'meta_data' => 'array',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }


    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }


//    /**
//     * Retrieve type instance
//     *
//     * @return AbstractType
//     */
//    public function getTypeInstance()
//    {
//        return $this->product->getTypeInstance();
//    }

    /**
     * Get product type value from base product
     */
    public function getTypeAttribute()
    {
        return $this->product->type;
    }


}
