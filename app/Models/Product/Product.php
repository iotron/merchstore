<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

// product type
    public const SIMPLE = 'simple';
    public const CONFIGURABLE = 'configurable';

    public const TYPE_OPTION = [
        self::SIMPLE => 'Simple',
        self::CONFIGURABLE => 'Configurable'
    ];


    // product status
    public const DRAFT = 'draft';

    public const REVIEW = 'review';

    public const PUBLISHED = 'published';

    public const StatusOptions = [
        self::DRAFT => 'Draft',
        self::REVIEW => 'Review',
        self::PUBLISHED => 'Published',
    ];



    protected $fillable = [
        'type',
        'name',
        'sku',
        'url_key',
        'featured',
        'visible_individually',
        'status',
        'base_price',
        'commission_percentage',
        'commission_amount',
        'price',
        'attribute_group_id',
        'parent_id',
        'min_range',
        'max_range'
    ];


    public function flat()
    {
        return $this->hasOne(ProductFlat::class, 'product_id', 'id');
    }




}
