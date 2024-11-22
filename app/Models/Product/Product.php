<?php

namespace App\Models\Product;

use App\Casts\MoneyCast;
use App\Helpers\ProductHelper\Support\ProductTypeSupportContract;
use App\Models\Category\Category;
use App\Models\Category\Theme;
use App\Models\Enums\Product\ProductStatusCast;
use App\Models\Enums\Product\ProductTypeCast;
use App\Models\Filter\FilterGroup;
use App\Models\Filter\FilterOption;
use App\Models\Promotion\SaleProduct;
use App\Models\Traits\CanBeScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property $name
 */
class Product extends Model implements HasMedia
{
    use CanBeScoped,HasFactory,InteractsWithMedia;

    protected ?ProductTypeSupportContract $typeInstance = null;

    // Pivot tables with product
    public const PRODUCT_CATEGORY_TABLE = 'product_categories';

    public const PRODUCT_THEME_TABLE = 'product_themes';



    //    protected $filterDataScope = 'ProductDataScope';

    protected $fillable = [
        'sku',
        'type',
        'name',
        'url',
        'quantity',
        'popularity',
        'view_count',
        'featured',
        'status',
        'is_returnable',
        'return_window',
        'tax_code',
        'tax_percent',
        'price',
        'filter_group_id',
        'parent_id',
        'min_range',
        'max_range',
    ];

    protected $casts = [
        'price' => MoneyCast::class,
        'is_returnable' => 'boolean',
        'return_window' => 'datetime',
        'type' => ProductTypeCast::class,
        'status' => ProductStatusCast::class
    ];

    /**
     * Relation Based On Other Class/Services
     */

    // Spatie Media Library Conversion
    public function registerMediaCollections(): void
    {

        $this->addMediaCollection('productDisplay')
            ->useFallbackUrl(asset('display.webp'));

        $this->addMediaCollection('productGallery')
            ->useFallbackUrl(asset('display.webp'))
            ->useFallbackUrl(asset('display.webp'), 'thumb_1')
            ->useFallbackUrl(asset('display.webp'), 'thumb_2')
            ->useFallbackUrl(asset('display.webp'), 'thumb_3');

    }

    /**
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(?Media $media = null): void
    {
//        if ($media && $media->extension === Manipulations::FORMAT_GIF) {
//            return;
//        }

        $this->addMediaConversion('optimized')
           // ->format(Manipulations::FORMAT_WEBP)
            ->withResponsiveImages()
            // uncomment in production
            ->nonQueued();
    }

    public function getTypeInstance(): ProductTypeSupportContract
    {
        if ($this->typeInstance) {
            return $this->typeInstance;
        }
        $this->typeInstance = app(config('project.product_types.'.$this->type->value.'.class'));
        $this->typeInstance->setProduct($this);

        return $this->typeInstance;
    }

    public function filterGroup()
    {
        return $this->belongsTo(FilterGroup::class, 'filter_group_id', 'id');
    }

    public function filterOptions(): BelongsToMany
    {
        return $this->belongsToMany(FilterOption::class, 'product_filter_options','product_id','filter_option_id');
    }

    /**
     * STOCK MANAGEMENT
     * in_stock stocks that belong to the product stock. For calculating in_stock/available stocks.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class, 'product_id');
    }

    public function availableStocks(): HasMany
    {
        return $this->stocks()->where('in_stock', true)->orderBy('priority');
    }

    public function minStock($count)
    {
        //dd($this->availableStocks->pluck('in_stock_quantity'));
        $availableMinStock = $this->availableStocks()->sum('in_stock_quantity');

        return min($availableMinStock ?? 0, $count);
    }

    //    public function allStocks(): \Illuminate\Database\Eloquent\Relations\HasMany
    //    {
    //        return $this->hasMany(ProductStock::class, 'product_id');
    //    }

    public function stockCount()
    {
        return $this->getTypeInstance()->totalQuantity();
    }

    /**
     * Sales Price
     * On Sale Products Management
     */
    public function sale_prices(): HasMany
    {
        return $this->hasMany(SaleProduct::class, 'product_id');
    }

    /**
     * Common Relations
     */
    public function flat(): HasOne
    {
        return $this->hasOne(ProductFlat::class, 'product_id', 'id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, Product::PRODUCT_CATEGORY_TABLE)->withPivot('base_category');
    }

    public function themes(): BelongsToMany
    {
        return $this->belongsToMany(Theme::class, Product::PRODUCT_THEME_TABLE)->withPivot('base_theme');
    }

    public function parentThemes()
    {
        return $this->belongsToMany(Theme::class, Product::PRODUCT_THEME_TABLE)->where('parent_id', null);
    }

    /**
     * Get the product variants that owns the product.
     */
    public function variants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(ProductFeedback::class, 'product_id');
    }
}
