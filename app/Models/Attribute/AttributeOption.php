<?php

namespace App\Models\Attribute;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
    use HasFactory;


    protected $fillable = [
        'admin_name',
        'swatch_value',
        'position',
    ];


    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }


}
