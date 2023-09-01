<?php

namespace App\Models\Promotion;

use App\Models\Customer\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VoucherCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'coupon_usage_limit',
        'usage_per_customer',
        'times_used',
        'type',
        'starts_from',
        'ends_till',
    ];


    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function usages(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'voucher_code_usages')->using(CouponUsage::class)->withPivot('times_used');
    }



}
