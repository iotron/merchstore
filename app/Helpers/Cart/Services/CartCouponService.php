<?php

namespace App\Helpers\Cart\Services;

use App\Helpers\Cart\Contracts\CartCouponServiceContract;
use App\Helpers\Cart\Contracts\CartServiceContract;
use App\Models\Promotion\VoucherCode;
use Illuminate\Database\Eloquent\Model;

class CartCouponService implements CartCouponServiceContract
{

    private CartServiceContract $cartService;
    private ?string $couponCode=null;
    protected bool $isValid=false;
    protected ?Model $couponModel=null;

    public function __construct(CartServiceContract $cartService)
    {
        $this->cartService = $cartService;
    }

    public function setCode(string $couponCode):void
    {
        $this->couponCode = $couponCode;
    }

    public function getCode(): ?string
    {
        return $this->couponCode;
    }





    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getModel(): ?Model
    {
        return $this->couponModel;
    }



    /**
     * @return void
     */
    public function destroy(): void
    {
        $this->couponCode = null;
        $this->couponModel = null;
        $this->isValid = false;
        $this->cartService->setCouponStatus(false);
    }

    /**
     * @param string|null $coupon_code
     * @return bool
     */
    public function validated(?string $coupon_code): bool
    {
        $this->couponCode = !is_null($coupon_code) ? $coupon_code : $this->couponCode;
        if (is_null($this->couponCode))
        {
            return false;
        }

        if (is_null($this->cartService->getProduct()))
        {
            return false;
        }

        $this->couponModel = is_null($this->couponModel) ?
            VoucherCode::with('voucher','usages')->firstWhere('code' , $coupon_code)
            : $this->couponModel;

        if (is_null($this->couponModel))
        {
            $this->cartService->setError('Invalid promo code for ' . $this->cartService->getProduct()->name);
            return false;
        }

        // Lists Of Validations
        if (!$this->checkValidation())
        {
            return false;
        }

        if (empty($this->cartService->getErrors()))
        {
            $this->isValid = true;
            $this->cartService->setCouponStatus(true);
            session(['coupon' => $coupon_code]);
            return true;
        }
        return true;

    }

    private function checkValidation(): bool
    {
    }


}
