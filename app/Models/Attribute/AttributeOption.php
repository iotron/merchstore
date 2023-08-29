<?php

namespace App\Models\Attribute;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AttributeOption extends Model
{
    use HasFactory;


    protected $fillable = [
        'admin_name',
        'swatch_value',
        'position',
    ];


    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }


    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class,'product_filter_options');
    }


}
