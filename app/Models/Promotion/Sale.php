<?php

namespace App\Models\Promotion;

use App\Casts\MoneyCast;
use App\Models\Customer\CustomerGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'starts_from',
        'ends_till',
        'status',
        'condition_type',
        'conditions',
        'end_other_rules',
        'action_type',
        'discount_amount',
        'sort_order',
    ];

    protected $casts = [
        'conditions' => 'array',
        'discount_amount' => MoneyCast::class,
    ];

    /**
     * Get the customer groups that will have the sale.
     */
    public function customer_groups()
    {
        return $this->belongsToMany(CustomerGroup::class, 'sale_customer_groups', 'sale_id');
    }

    /**
     * Get the Sale Products in the sale.
     */
    public function sale_products(): HasMany
    {
        return $this->hasMany(SaleProduct::class);
    }
}
