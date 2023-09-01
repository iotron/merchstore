<?php

namespace App\Helpers\Cart\Services;

use App\Helpers\Cart\Contracts\CartServiceContract;
use Illuminate\Database\Eloquent\Model;

class CartCouponService
{

    private CartServiceContract $cartService;
    private ?string $couponCode=null;
    protected bool $isValid=false;
    protected ?Model $couponModel=null;

    public function __construct(CartServiceContract $cartService)
    {
        $this->cartService = $cartService;
    }

    public function getModel(): ?Model
    {
        return $this->couponModel;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function validated(mixed $coupon):void
    {
        dd($coupon);
    }

}
