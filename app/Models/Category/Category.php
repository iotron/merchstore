<?php

namespace App\Models\Category;

use App\Models\Product\Product;
use App\Models\Traits\HasChildren;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory,HasChildren;


    protected $fillable = [
        'name',
        'url',
        'parent_id',
        'image_path',
        'status',
        'is_visible_on_front',
        'view_count',
        'order',
        'desc',
        'meta_data',
        'hsn_4',
        'hsn_8',
        'gst',
    ];

    protected $casts = [
        'meta_data' => AsArrayObject::class,
        'hsn_8' => AsCollection::class,
        'gst' => AsCollection::class,
    ];

    public function getRouteKeyName(): string
    {
        return 'url';
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id', 'id');
    }


    public function products()
    {
        return $this->belongsToMany(Product::class, Product::PRODUCT_CATEGORY_TABLE);
    }





}
