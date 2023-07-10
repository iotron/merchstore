<?php

namespace App\Models\Promotion;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CouponUsage extends Pivot
{
    protected $table = 'voucher_code_usage';

    public function times_used(): BelongsTo
    {
        return $this->belongsTo(VoucherCode::class, 'voucher_code_id');
    }
}
