<?php

namespace App\Helpers\Promotion\Sales;

use App\Helpers\Money\Money;
use App\Models\Category\Category;
use App\Models\Product\Product;
use App\Models\Promotion\Sale;
use App\Models\Promotion\SaleProduct;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class ProductSaleHelper
{

    public function reindexSaleableProducts(): void
    {
        $this->cleanIndex();
    }

    private function cleanIndex(): void
    {
        $allSales = Sale::where('starts_from', '<=', Carbon::now()->format('Y-m-d'))->orWhereNull('starts_from')->orWhere('ends_till', '>=', Carbon::now()->format('Y-m-d'))->orWhereNull('ends_till')->orderBy('sort_order', 'asc')->where('status', 1)->get();
        foreach ($allSales as $sale) {
            $this->insertSaleProduct($sale);
        }
    }


    private function insertSaleProduct(Sale $sale, $product = null): void
    {



        $rows = [];
        $productIds = $this->getMatchingProductIds($sale, $product);

        $startsFrom = $sale->starts_from ? Carbon::createFromTimeString($sale->starts_from.' 00:00:01') : null;
        $endsTill = $sale->ends_till ? Carbon::createFromTimeString($sale->ends_till.' 23:59:59') : null;

        $productCollection = Product::whereIn('id', $productIds)->get();


        foreach ($productCollection as $product) {


            // Group Relation Between CatalogRule with Customer need to fix
            foreach ($sale->customer_groups()->pluck('id') as $customerGroupId) {
                $rows[] = [
                    'starts_from' => $startsFrom,
                    'ends_till' => $endsTill,
                    'sale_id' => $sale->id,
                    'customer_group_id' => $customerGroupId,
                    'product_id' => $product->id,
                    'discount_amount' => $sale->discount_amount->getAmount(),
                    'sale_price' => $this->calculate($sale, $product),
                    'action_type' => $sale->action_type,
                    'end_other_rules' => $sale->end_other_rules,
                    'sort_order' => $sale->sort_order,
                ];
            }
        }


        // Ready For Insert/Update CatalogRule Product with Discounted

        $this->storeRecord($rows);

        // send notification
        Notification::make()
            ->title('Reindexing Product Sales successfully')
            ->success()
            ->seconds(7)
            ->send();

    }


    protected function storeRecord(array $data)
    {
        $storeProduct = SaleProduct::upsert($data, ['customer_group_id','product_id'], ['starts_from',
            'ends_till',
            'discount_amount',
            'action_type',
            'end_other_rules',
            'sort_order',
            'sale_price',
            'sale_id',
            'customer_group_id',
            'product_id', ]);
    }


    public function calculate(Sale $sale, Product $product)
    {

        $price = isset($product->price) && ! empty($product->price->getAmount()) ? $product->price: $sale->discount_amount;

        return match ($sale->action_type) {
            'to_fixed' => min($sale->discount_amount->getAmount(), $price->getAmount()),
            'to_percent' => $price->multiplyOnce($sale->discount_amount->divideOnce(100)->getValue())->getAmount(),
            'by_fixed' => max(0, $price->subtract($sale->discount_amount)->getAmount()),
            'by_percent' => $price->multiplyOnce((new Money(100))->subtract($sale->discount_amount->divideOnce(100))->getValue())->getAmount(),
        };
    }



    private function getMatchingProductIds(Sale $sale, mixed $product): array
    {
        // Prepare Conditions
        $bag = $this->prepareBag($sale);
       // Fetch Matched Products
        $allCatProducts = $this->resolveCategoryProducts($sale,$bag);
        $allQueryProducts = $this->resolveQueryableProducts($sale,$bag);
        // Unique Product IDs
        $uniques = collect(array_replace(array_keys($allQueryProducts), array_keys($allCatProducts)))->unique();
        $ids = $uniques->values()->all();
        if (isset($ids[0])) {
            if ($ids[0] === 0) {
                unset($ids[0]);
            }
        }
        return $ids;
    }




    protected function prepareBag(Sale $sale)
    {
        $conditionList = (array) $sale->conditions;
        $bag = [];
        // Lets Check
        foreach ($conditionList as $condition)
        {
            // First Check Condition Format & Value
            if (! empty($condition['attribute']) && ! empty($condition['operator']) && ! empty($condition['value']))
            {


                $key = $condition['attribute'];
                $chunks = explode('|', $condition['attribute']);

                if ($chunks[1] === 'category_id' || $chunks[1] === 'attribute_group_id') {
                    if ($chunks[1] === 'category_id') {
                        if ($condition['operator'] === '=') {
                            $allCats = Category::with('products')->whereIn('id', $condition['value'])->get();
                        } else {
                            $allCats = Category::with('products')->whereNotIn('id', $condition['value'])->get();
                        }

                        // Fill Array With All Categories Products
                        foreach ($allCats as $cats) {
                            if ($cats->products) {
                                $bag['cat'][] = $cats->products;
                            }
                        }
                    }
                } else {
                    $bag['att'][] = [
                        'column' => strtolower($chunks[1]),
                        'operator' => $condition['operator'],
                        'value' => $condition['value'],
                    ];
                }



            }
        }

        return $bag;
    }




    private function resolveCategoryProducts(Sale $sale,array $bag)
    {
        $allCatProducts = [];
        if (isset($bar['cat']) && ! empty($bag['cat'])) {
            foreach ($bag['cat'] as $collection) {
                $allCatProducts = array_merge($allCatProducts, $collection->pluck('id')->flip()->toArray());
            }
        }
        return $allCatProducts;
    }

    private function resolveQueryableProducts(Sale $sale,array $bag)
    {
        $allQueryProducts = [];
        $availableColumns = Product::find(1)->first()->getFillable();

        if (! empty($bag['att'])) {
            $query = Product::with('flat')->latest()->where('status', true);
            foreach ($bag['att'] as $item) {
                if (in_array($item['column'], $availableColumns)) {
                    if ($sale->condition_type) {
                        $query = $query->where($item['column'], $item['operator'], $item['value']);
                    } else {
                        $query = $query->orWhere($item['column'], $item['operator'], $item['value']);
                    }
                }
            }
            $allQueryProducts = $query->get()->pluck('id')->flip()->toArray();
        }
        return $allQueryProducts;
    }


}
