<?php

namespace App\Helpers\ProductHelper\Support;

use App\Models\Product\Product;
use Illuminate\Support\Str;

abstract class AbstractSupportProductSupport implements ProductTypeSupportContract
{
    protected Product $product;

    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    public function totalQuantity(): int
    {
        return $this->product->quantity;
    }

    public function create(array $data): bool|Product
    {
        // Prepare Data
        $data = $this->prepareDataForProductCreation($data);

        // Create Product
        return Product::create($data);
    }

    public function update(int $id, array $data, string $attribute = 'id'): bool|Product
    {

        $product = Product::firstWhere('id', $id);
        if (is_null($product)) {
            return false;
        }
        //Update Product
        $product->fill($data)->save();
        // Update Product Flat
        $product->flat->fill($data)->save();

        return $product;
    }

    private function prepareDataForProductCreation(array $data): array
    {
        return array_merge($data, [
            //            'vendor_id' => auth()->user()->id,
            'url' => Str::slug($data['name'].'-'.now()),
        ]);
    }
}
