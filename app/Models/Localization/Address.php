<?php

namespace App\Models\Localization;

use App\Models\Customer\Customer;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property $id;
 * @property $address_1;
 * @property $address_2;
 */
class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'pickup_location', //
        'name',
        'email',
        'contact',
        'alternate_contact',
        'type',
        'address_1',
        'address_2',
        'landmark',
        'city',
        'postal_code',
        'state',
        'default',
        'priority',
        'country_code',
        'addressable_id',
        'addressable_type',
    ];


    protected $casts = [
        'default' => 'bool'
    ];


    public static function boot()
    {
        parent::boot();
        //For update & create functions
        static::saving(function ($address) {
            if ($address->default) {
                $address->addressable->addresses()->update([
                    'default' => false,
                ]);
            }
        });
    }



    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }


    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class,  'country_code','iso_code_2');
    }


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'id', 'addressable_id');
    }



//    public function products(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
//    {
//        return $this->belongsToMany(Product::class, 'product_stocks')->withPivot('quantity');
//    }

//    public function orderProducts()
//    {
//        return $this->hasMany(OrderProduct::class);
//    }




}
