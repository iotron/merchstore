<?php

namespace App\Models\Promotion;

use App\Helpers\Money\MoneyCast;
use App\Models\Customer\CustomerGroup;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class SaleProduct extends Model
{
    use HasFactory,Prunable;

    protected $fillable = [

        'starts_from',
        'ends_till',
        'end_other_rules',
        'action_type',
        'sale_price',
        'discount_amount',
        'sort_order',
    ];

    protected $casts = [
        'sale_price' => MoneyCast::class,
        'discount_amount' => MoneyCast::class,
    ];

    public function prunable()
    {
        return static::where('created_at', '<=', now()->subMonth());
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function customer_group()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }
}
