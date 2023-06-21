<?php

namespace App\Helpers\ProductHelper\Support;

use App\Models\Product\Product;

interface ProductTypeSupportContract
{

    public function create(array $data):bool|Product;

    public function update(int $id,array $data,string $attribute='id'):bool|Product;

    public function isSaleable():bool;

    public function haveSufficientQuantity():int;

}
