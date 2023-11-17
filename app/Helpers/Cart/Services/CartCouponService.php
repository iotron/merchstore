<?php

namespace App\Helpers\Cart\Services;

use App\Helpers\Cart\Contracts\CartCouponServiceContract;
use App\Helpers\Cart\Contracts\CartServiceContract;
use App\Helpers\Cart\Services\Voucher\VoucherCartService;
use App\Models\Promotion\VoucherCode;
use Carbon\Carbon;
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
            $this->cartService->setError('no product found in your cart');
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
            $this->cartService->setError('coupon validation failed!');
            return false;
        }

//        dd($this->cartService->getErrors());

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


        if ($this->validVoucher() &&
            $this->validSchedule() &&
            $this->validTotalUsage() &&
            $this->validMaxUsage() &&
            $this->validateMinimumQuantity() &&
            $this->validCustomerGroup()
        )

        {


            // Validate Rules And Conditions
            // Currently Condition Not Validate
//            $voucherService = new VoucherCartService($this->couponModel->voucher,$this->cartService);
//            dd($voucherService->validate());
//            if ($voucherService->validate())
//            {
//                return true;
//            }


            // return validate
            return true;

        }

        return false;
    }


    // Validations

    protected function validVoucher(): bool
    {
        // voucher status
        if (!$this->couponModel->voucher->status) {
            $this->cartService->setError('Coupon code not found for ' . $this->cartService->getProduct()->name);
            return false;
        }
        return true;
    }




    protected function validSchedule(): bool
    {
       // Check Start Date
        if (!Carbon::parse($this->couponModel->starts_from)->lessThanOrEqualTo(now()))
        {
            $this->cartService->setError('coupon will be available ' . Carbon::parse($this->couponModel->starts_from)->diffForHumans() . 'days after');
            return false;
        }
        // Check End Time
        if (!Carbon::parse($this->couponModel->ends_till)->greaterThanOrEqualTo(now()))
        {
            $this->cartService->setError('coupon expired ' . Carbon::parse($this->couponModel->ends_till)->diffForHumans() . 'days ago');
            return false;
        }

        return true;
    }

    protected function validMaxUsage(): bool
    {
        // max usage limit reached for this customer
        $customerUsage = $this->couponModel->usages()->where('customer_id', $this->cartService->getCustomer()->id)->pivot->times_used ?? null;

        // validate coupon usage
        if (!is_null($customerUsage)) {
            if ($customerUsage > $this->couponModel->usage_per_customer) {
                $this->cartService->setError('already used' . $customerUsage . 'times');
                return false;
            }
        }
        return true;
    }


    // need to refactor again.
    protected function validTotalUsage(): bool
    {

        // max total usages reached
        if ($this->couponModel->times_used > $this->couponModel->coupon_usage_limit)
        {
            $this->cartService->setError('max coupon usage reached');
            return false;
        }
        return true;
    }



    protected function validateMinimumQuantity(): bool
    {
        // Check If Match with Minimum Quantity in Cart
        if ($this->cartService->getTotalQuantity() < $this->couponModel->min_quantity)
        {
            $this->cartService->setError('coupon not fulfill with minimum ticket requirement');
            return false;
        }
        return true;
    }

    private function validCustomerGroup(): bool
    {
        $customerGroup = $this->couponModel->voucher->customer_groups()->firstWhere('customer_group_id', $this->cartService->getCustomer()->customer_group_id);

        if (is_null($customerGroup)) {
            $this->cartService->setError('voucher code not applicable for your group');
            return false;
        }
        return true;
    }


}
