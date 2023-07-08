<?php

namespace App\Models\Localization;

use App\Models\Customer\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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
        return $this->belongsTo(Country::class, 'iso_code_2', 'country_code');
    }


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'id', 'addressable_id');
    }



}
