<?php

namespace App\Models\Localization;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'iso_code_2',
        'iso_code_3',
        'isd_code',
        'address_format',
        'postcode_required',
    ];

    public function address()
    {
        return $this->hasMany(Address::class, 'country_code', 'iso_code_2');
    }
}
