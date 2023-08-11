<?php

namespace App\Models\Promotion;

use App\Helpers\Money\MoneyCast;
use App\Models\Customer\CustomerGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Voucher extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'description',
        'starts_from',
        'ends_till',
        'status',
        'usage_per_customer',
        'coupon_usage_limit',
        'times_used',

        'condition_type',
        'conditions',
        'end_other_rules',
        'action_type',

        'discount_amount',
        'discount_quantity',
        'discount_step',
        'apply_to_shipping',
        'free_shipping',
        'sort_order',

    ];

    protected $casts = [
        'conditions' => 'array',
        'discount_amount' => MoneyCast::class
    ];




    public function customer_groups(): BelongsToMany
    {
        return $this->belongsToMany(CustomerGroup::class, 'voucher_customer_groups');
    }

    public function coupons()
    {
        return $this->hasMany(VoucherCode::class);
    }


    public function coupon_code(): HasOne
    {
        return $this->coupons()->where('is_primary', 1);
    }



}
