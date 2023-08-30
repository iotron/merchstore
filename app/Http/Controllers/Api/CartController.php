<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Cart\Cart;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\CartStoreRequest;
use App\Http\Requests\Cart\CartUpdateRequest;
use App\Http\Resources\Cart\CartResource;
use App\Models\Product\Product;
use Illuminate\Http\JsonResponse;
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


    public function store(CartStoreRequest $request, Cart $cart): JsonResponse
    {
        $cart->addBulk($request->product);
        $metaData = $cart->getMeta();
        if ($cart->getErrors()) {
            return response()->json(['success' => false, 'message' => $cart->getErrors()], 403);
        } else {
            return response()->json(['success' => true, 'message' => 'product added successfully','data' => new CartResource($metaData)], 200);
        }
    }


    public function add(Product $product, Request $request, Cart $cart): JsonResponse
    {
        if ($cart->products()->count())
        {
            if (!$cart->products()->contains('product_id',$product->id))
            {
                return response()->json(['success' => false, 'message' => 'another product present with product id '], 403);
            }
        }

        $cart->add($product->id, $request->quantity);
        $metaData = $cart->getMeta();

        if ($cart->getErrors()) {
            return response()->json(['success' => false, 'message' => $cart->getErrors()], 403);
        } else {
            return response()->json(['success' => true, 'message' => 'product added successfully','data' => new CartResource($metaData)], 200);
        }
    }


    public function update(Product $product, CartUpdateRequest $request, Cart $cart): JsonResponse
    {
        $cart->update($product->id, $request->quantity);
        if ($cart->getErrors()) {
            return response()->json(['success' => false, 'message' => $cart->getErrors()], 403);
        } else {
            return response()->json(['success' => true, 'message' => 'Quantity updated for ' . $product->name . '!', 'data' => new CartResource($cart->getMeta())], 200);
        }
    }

    public function destroy(Product $product, Cart $cart): JsonResponse
    {
        // $cart->empty();
        $cart->delete($product->id);
        if ($cart->getErrors()) {
            return response()->json(['success' => false, 'message' => $cart->getErrors()], 403);
        } else {
            return response()->json(['success' => true, 'message' => $product->name . ' removed!', 'data' => new CartResource($cart->getMeta())], 200);
        }
    }




}
