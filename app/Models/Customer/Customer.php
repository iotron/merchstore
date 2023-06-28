<?php

namespace App\Models\Customer;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Customer extends Model
{
    use HasFactory;



    protected $fillable = [
        'name',
        'email',
        'contact',
        'password',
        'email_verified',
//        'referrer',
//        'has_push',
        //'whatsapp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function cart(): BelongsToMany
    {
        return $this->belongsToMany(Product::class,'cart_customer')
            ->withPivot('quantity','discount')->withTimestamps();
    }




}
