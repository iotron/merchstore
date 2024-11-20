<?php

namespace App\Services\CartService\Support;

use App\Models\Promotion\VoucherCode;
use Carbon\Carbon;

class CartCouponValidator
{

    protected VoucherCode $couponModel;
    protected int $totalQuantity = 0;
    protected array $errors = [];



    public static function make(VoucherCode $record): static
    {
        $instance = new static();
        $instance->couponModel = $record;
        return $instance;
    }


    public function validate(int $totalQuantity = 0):bool
    {
        $this->totalQuantity = $totalQuantity;
        return $this->validVoucherStatus() &&
            $this->validSchedule() &&
            $this->validTotalUsage() &&
            $this->validMaxUsage() &&
            $this->validateMinimumQuantity() &&
            $this->validCustomerGroup();
    }

    public function getErrors():array
    {
        return $this->errors;
    }

    private function validVoucherStatus(): bool
    {
        if (! $this->couponModel->voucher->status) {
            $this->errors[] = 'The coupon code is either invalid or inactive.';
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

    private function validTotalUsage(): bool
    {
        // max total usages reached
        if ($this->couponModel->times_used > $this->couponModel->coupon_usage_limit) {
            $this->errors [] = 'max coupon usage reached';
            return false;
        }
        return true;
    }

    private function validMaxUsage(): bool
    {
        // max usage limit reached for this customer
        $customerUsage = $this->couponModel->usages?->pivot->times_used ?? null;
        // validate coupon usage
        if (! is_null($customerUsage)) {
            if ($customerUsage > $this->couponModel->usage_per_customer) {
                $this->errors[] = 'already used'.$customerUsage.'times';
                return false;
            }
        }
        return true;
    }

    private function validateMinimumQuantity(): bool
    {
        // Check If Match with Minimum Quantity in Cart
        if ($this->totalQuantity < $this->couponModel->min_quantity) {
            $this->errors [] = 'coupon not fulfill with minimum ticket requirement';
            return false;
        }

        return true;
    }

    private function validCustomerGroup()
    {
        $customerGroup = $this->couponModel?->voucher?->customer_groups;
        if (is_null($customerGroup)) {
            $this->errors [] = 'voucher code not applicable for your group';
            return false;
        }
        return true;
    }


}
