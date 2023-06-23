<?php

namespace App\Models\Product;

use App\Helpers\FilterHelper\Filterable;
use App\Helpers\Money\MoneyCast;
use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia,Filterable;


    public const PRODUCT_CATEGORY_TABLE='product_categories';

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

    protected $filterDataScope = 'ProductDataScope';

    protected $fillable = [
        'type',
        'name',
        'sku',
        'url_key',
        'featured',
        'visible_individually',
        'status',
        'base_price',
        'price',
        'attribute_group_id',
        'parent_id',
        'min_range',
        'max_range'
    ];

    protected $casts = [
        'base_price' => MoneyCast::class,
        'price' => MoneyCast::class,
    ];




    public function flat(): HasOne
    {
        return $this->hasOne(ProductFlat::class, 'product_id', 'id');
    }


    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, Product::PRODUCT_CATEGORY_TABLE)->withPivot('base_category');
    }

    /**
     * Get the product variants that owns the product.
     */
    public function variants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class, 'parent_id');
    }


//
//    public function getTypeInstance()
//    {
//        if ($this->typeInstance) {
//            return $this->typeInstance;
//        }
//        $this->typeInstance = app(config('project.product_types.' . $this->type . '.class'));
//
//        $this->typeInstance->setProduct($this);
//
//        return $this->typeInstance;
//    }
//
//
//
//    /**
//     * STOCK MANAGEMENT
//     * All the stocks that belong to the product. For stock history/CRUD.
//     */
//
//    public function allStocks()
//    {
//        return $this->hasMany(ProductStock::class, 'product_id');
//    }
//
//    /**
//     * STOCK MANAGEMENT
//     * in_stock stocks that belong to the product. For calculating in_stock/available stocks.
//     */
//
//    public function availableStocks()
//    {
//        return $this->allStocks()->where('in_stock', true)->orderBy('priority');
//    }
//
//    public function stockCount()
//    {
//        return $this->getTypeInstance()->totalQuantity();
//    }
//    public function minStock($count)
//    {
//        return min($this->quantity, $count);
//    }
//



}
