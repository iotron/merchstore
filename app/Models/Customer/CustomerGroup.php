<?php

namespace App\Models\Customer;

use App\Models\Promotion\Sale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    public function customer(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function sale()
    {
        return $this->belongsToMany(Sale::class, 'sale_customer_groups', 'customer_group_id');
    }
}
