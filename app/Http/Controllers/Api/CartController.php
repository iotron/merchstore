<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Cart\Cart;
use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\CartResource;
use App\Models\Product\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:customer');
    }


    public function index(Request $request, Cart $cart): CartResource
    {
        return new CartResource($cart->getMeta());
    }


    public function add(Product $product, Request $request, Cart $cart)
    {

        if ($cart->products()->count())
        {
            if (!$cart->products()->contains('product_id',$product->id))
            {
                return response()->json(['success' => false, 'message' => 'another event ticket present with ticket id '], 403);
            }
        }

        $cart->add($product->id, $request->quantity);
        $metaData = $cart->getMeta();
        //  dd($metaData);

        if ($cart->getErrors()) {
            return response()->json(['success' => false, 'message' => $cart->getErrors()], 403);
        } else {
            return response()->json(['success' => true, 'message' => 'ticket added successfully','data' => new CartResource($metaData)], 200);
        }
    }




}
