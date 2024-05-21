<?php

namespace App\Models\Filter;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FilterGroup extends Model
{
    use HasFactory;

    public const STATIC = 'static';

    public const FILTERABLE = 'filterable';

    public const TYPE_OPTIONS = [
        self::STATIC => 'Static',
        self::FILTERABLE => 'Filterable',
    ];

    protected $fillable = [
        'admin_name',
        'code',
        'position',
        'type',
    ];

    public function filters(): BelongsToMany
    {
        return $this->belongsToMany(Filter::class, 'filter_group_mappings', 'filter_group_id', 'filter_id');
    }

    public function product(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
