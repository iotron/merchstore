<?php

namespace App\Models\Customer;

use App\Models\Localization\Address;
use App\Models\Payment\Payment;
use App\Models\Product\Product;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;


class Customer extends Authenticatable implements MustVerifyEmail
{
    use HasFactory,Notifiable;



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



    /**
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }


    public function socials()
    {
        return $this->hasMany(CustomerSocial::class, 'customer_id', 'id');
    }


    /**
     * @return MorphMany
     */
    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }


    public function cart(): BelongsToMany
    {
        return $this->belongsToMany(Product::class,'cart_customer')
            ->withPivot('quantity','discount')->withTimestamps();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class,'customer_id','id');
    }




}
