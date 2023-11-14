<?php

namespace App\Models\Filter;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FilterOption extends Model
{
    use HasFactory;


    protected $fillable = [
        'display_name',
        'swatch_value',
        'position',
    ];



    public function filter(): BelongsTo
    {
        return $this->belongsTo(Filter::class, 'filter_id', 'id');
    }


    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class,'product_filter_options');
    }



}
