<?php

namespace App\Http\Controllers;

use App\Models\Category\Category;
use App\Models\Product\Product;
use Illuminate\Http\Request;

class TestController extends Controller
{


    public function index()
    {


        $product = Product::find(1);

        dd($product->availableStocks()->sum('in_stock_quantity'));


    }


}
