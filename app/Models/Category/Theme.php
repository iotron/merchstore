<?php

namespace App\Models\Category;

use App\Models\Traits\HasChildren;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Theme extends Model
{
    use HasFactory, HasChildren;

    protected $fillable = [
            'name',
            'url',
            'parent_id',
    ];


    public function children(): HasMany
    {
        return $this->hasMany(Theme::class, 'parent_id', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id', 'id');
    }
}
