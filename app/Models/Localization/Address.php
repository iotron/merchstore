<?php

namespace App\Models\Localization;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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




    public function addressable()
    {
        return $this->morphTo();
    }


    public function country()
    {
        return $this->belongsTo(Country::class, 'iso_code_2', 'country_code');
    }




}
