<?php

namespace App\Models\Filter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filter extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'display_name',
        'type',
        'desc',
        'validation',
        'position',
        'is_filterable',
        'is_configurable',
        'is_user_defined',
        'is_required',
        'is_visible_on_front',
    ];

    protected $casts = [
        'validation' => 'array',
        'is_filterable' => 'boolean',
        'is_configurable' => 'boolean',
        'is_user_defined' => 'boolean',
        'is_required' => 'boolean',
        'is_visible_on_front' => 'boolean',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(FilterOption::class, 'filter_id', 'id');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(FilterGroup::class, 'filter_group_mappings', 'filter_group_id', 'filter_id');
    }

    public function scopeConfigurable($query)
    {
        return $query->where('is_configurable', true)->orderBy('position');
    }
}
