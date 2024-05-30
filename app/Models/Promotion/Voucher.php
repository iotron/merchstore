<?php

namespace App\Models\Promotion;

use App\Models\Customer\CustomerGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Voucher extends Model
{
    use HasFactory;

    public const MATCH_ALL = 1;

    public const MATCH_ANY = 0;

    public const CONDITION_TYPE = [
        self::MATCH_ALL => 'Match All Conditions',
        self::MATCH_ANY => 'Match Any Condition',
    ];

    public const ACTION_BY_PERCENTAGE = 'by_percent';

    public const ACTION_BY_FIXED = 'by_fixed';

    public const ACTION_CART_FIXED = 'cart_fixed';

    public const ACTION_BY_X_GET_Y = 'buy_x_get_y';

    public const ACTION_CART_PERCENTAGE = 'cart_percent';

    public const ACTION_TYPES = [
        self::ACTION_BY_PERCENTAGE => 'Percentage of Product Price',
        self::ACTION_BY_FIXED => 'Fixed Amount',
        self::ACTION_CART_FIXED => 'Fixed Amount to Whole Cart',
        self::ACTION_CART_PERCENTAGE => 'Cart Percentage',
    ];

    public const SHIPPING_TRUE = 1;

    public const SHIPPING_FALSE = 0;

    public const APPLY_TO_SHIPPING_OPTIONS = [
        self::SHIPPING_FALSE => 'No',
        self::SHIPPING_TRUE => 'Yes',
    ];

    public const FREE_SHIPPING_OPTIONS = [
        self::SHIPPING_FALSE => 'No',
        self::SHIPPING_TRUE => 'Yes',
    ];

    public const END_OTHER_RULE_TRUE = 1;

    public const END_OTHER_RULE_FALSE = 0;

    public const END_OTHER_RULE_OPTION = [
        self::END_OTHER_RULE_FALSE => 'No',
        self::END_OTHER_RULE_TRUE => 'Yes',
    ];

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
        'discount_amount' => MoneyCast::class,
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
