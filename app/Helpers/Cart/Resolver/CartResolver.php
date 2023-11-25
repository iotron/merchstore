<?php

namespace App\Helpers\Cart\Resolver;

use App\Helpers\Cart\Contracts\CartServiceContract;

class CartResolver
{


    protected CartServiceContract $cart;

    public function __construct(CartServiceContract $cartService)
    {
        $this->cart = $cartService;
    }

    public function resolve()
    {
        dd($this->cart);
    }


}
