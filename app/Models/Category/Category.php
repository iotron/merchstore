<?php

namespace App\Models\Category;

use App\Models\Product\Product;
use App\Models\Traits\HasChildren;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Category extends Model implements HasMedia
{
    use HasRecursiveRelationships,HasFactory, InteractsWithMedia;
    public const CATEGORY_IMAGE = 'categoryImage';

    protected $fillable = [
        'name',
        'url',
        'parent_id',
        'status',
        'is_visible_on_front',
        'view_count',
        'order',
        'desc',
        'meta_data',
        'banners',
        //        'hsn_4',
        //        'hsn_8',
        //        'gst',
    ];

    protected $casts = [
        'meta_data' => AsArrayObject::class,
        //        'hsn_8' => AsCollection::class,
        //        'gst' => AsCollection::class,
        'banners' => AsArrayObject::class,
    ];

    // Spatie Media Library Conversion
//    public function registerMediaCollections(): void
//    {
//        $this->addMediaCollection('categoryGallery')
//            ->useFallbackUrl(asset('display.webp'))
//            ->useFallbackUrl(asset('display.webp'), 'thumb_1')
//            ->useFallbackUrl(asset('display.webp'), 'thumb_2')
//            ->useFallbackUrl(asset('display.webp'), 'thumb_3');
//
//        $this->addMediaCollection('banners')
//            ->useFallbackUrl(asset('display.webp'));
//    }
//
//    public function registerMediaConversions(?Media $media = null): void
//    {
//        if ($media && $media->extension === Manipulations::FORMAT_GIF) {
//            return;
//        }
//
//        $this->addMediaConversion('optimized')
//            ->format(Manipulations::FORMAT_WEBP)
//            ->withResponsiveImages()
//            // uncomment in production
//            ->nonQueued();
//    }

    public function getRouteKeyName(): string
    {
        return 'url';
    }

//    public function children(): HasMany
//    {
//        return $this->hasMany(Category::class, 'parent_id', 'id');
//    }
//
//    public function parent(): BelongsTo
//    {
//        return $this->belongsTo(static::class, 'parent_id', 'id');
//    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, Product::PRODUCT_CATEGORY_TABLE);
    }
}
