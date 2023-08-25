<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSocial extends Model
{
    use HasFactory;


    protected $fillable = [
        'social_id',
        'service',
        'token'
    ];

    /**
     * @return HasOne
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class,'id','customer_id');
    }




}
