<?php

namespace App\Services\CartService\Support;

use App\Models\Promotion\VoucherCode;
use Carbon\Carbon;

class CartCouponValidator
{

    protected VoucherCode $couponModel;
    protected array $errors = [];



    public static function make(VoucherCode $record): static
    {
        $instance = new static();
        $instance->couponModel = $record;
        return $instance;
    }


    public function validate():bool
    {
        return $this->validVoucherStatus() &&
            $this->validSchedule() &&
            $this->validTotalUsage() &&
            $this->validMaxUsage() &&
            $this->validateMinimumQuantity() &&
            $this->validCustomerGroup();
    }

    private function validVoucherStatus(): bool
    {
        if (! $this->couponModel->voucher->status) {
            $this->errors [] = 'Coupon code not found!';
            return false;
        }
        return true;
    }

    private function validSchedule(): bool
    {
        // Check Start Date
        if (! Carbon::parse($this->couponModel->starts_from)->lessThanOrEqualTo(now())) {
            $this->errors [] =  'coupon will be available '.Carbon::parse($this->couponModel->starts_from)->diffForHumans().'days after';
            return false;
        }
        // Check End Time
        if (! Carbon::parse($this->couponModel->ends_till)->greaterThanOrEqualTo(now())) {
            $this->errors[] = 'coupon expired '.Carbon::parse($this->couponModel->ends_till)->diffForHumans().'days ago';
            return false;
        }
        return true;
    }

    private function validTotalUsage()
    {
        dd($this->couponModel,$this->couponModel->usages);
        // max usage limit reached for this customer
        $customerUsage = $this->couponModel->usages()->where('customer_id', $this->cartService->getCustomer()->id)->pivot->times_used ?? null;

        // validate coupon usage
        if (! is_null($customerUsage)) {
            if ($customerUsage > $this->couponModel->usage_per_customer) {
                $this->cartService->setError('already used'.$customerUsage.'times');

                return false;
            }
        }

        return true;
    }

    private function validMaxUsage()
    {
    }

    private function validateMinimumQuantity()
    {
    }

    private function validCustomerGroup()
    {
    }


}
