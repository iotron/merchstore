<?php

namespace App\Models\Attribute;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;


    protected $fillable = [
        'code',
        'admin_name',
        'type',
        'position',
        'validation',
        'is_filterable',
        'is_configurable',
        'is_visible_on_front',
        'is_required',
        'is_user_defined',
    ];

    protected $casts = [
        'validation' => 'array',
    ];




    public function options()
    {
        return $this->hasMany(AttributeOption::class, 'attribute_id', 'id');
    }

    public function groups()
    {
        return $this->belongsToMany(AttributeGroup::class, 'attribute_group_mappings');
    }

    public function scopeConfigurable($query)
    {
        return $query->where('is_configurable', true)->orderBy('position');
    }




}
