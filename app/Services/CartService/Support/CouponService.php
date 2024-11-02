<?php

namespace App\Services\CartService\Support;

use App\Models\Customer\Customer;
use App\Models\Events\EventPromo;
use Carbon\Carbon;

class CouponService
{
    protected ?EventPromo $eventPromo = null;
    protected array $errors = [];
    protected Customer $customer;
    protected int $cartQty = 0;

    public static function make(EventPromo $eventPromo): static
    {
        $instance = new static();
        $instance->eventPromo = $eventPromo;
        $instance->eventPromo->loadMissing('event');
        return $instance;
    }




    public function getErrors():array
    {
        return $this->errors;
    }


    public function isValidFor(Customer $customer,int $cartCurrentQty = 0):bool
    {
        $this->customer = $customer;
        $this->cartQty = $cartCurrentQty;
        if ($this->validSchedule() &&
            $this->validVoucher() &&
            $this->validateMaxUsage() &&
            $this->validateUserUsage() &&
            $this->validateMinimumQuantity()) {
            return true;
        }

        return false;
    }


    /**
     * Check Promo
     * Start Time And End Time
     * with Status
     * @return bool
     */
    protected function validSchedule(): bool
    {
        if ($this->eventPromo->start_time >= now() || $this->eventPromo->end_time <= now() || ! $this->eventPromo->status) {
            $this->errors [] = 'coupon code expired for '.$this->eventPromo->event->name;
            return false;
        }
        return true;
    }


    /**
     * Check Expiration
     * @return bool
     */
    protected function validVoucher(): bool
    {
        if ($this->eventPromo->end_time < Carbon::now()->format('Y-m-d H:m:s')) {
            $this->errors [] = 'coupon expired '.Carbon::parse($this->eventPromo->end_time)->diffForHumans().'days ago';
            return false;
        }
        return true;
    }


    protected function validateMaxUsage():bool
    {
        $usage = $this->eventPromo->usages()->sum('times_used') ?? null;
        if ($usage >= $this->eventPromo->max_usage_limit) {
            $this->errors [] = 'coupon max usage reached';
            return false;
        }
        return true;
    }

    protected function validateUserUsage(): bool
    {
        $usage = $this->eventPromo->usages()->firstWhere('customer_id', $this->customer->id)->pivot->times_used ?? null;
        if ($usage)
        {
            if ($usage >= $this->eventPromo->usage_per_customer) {
                $this->errors[] = 'coupon already used for '.$usage.' times';
                return false;
            }
        }
        return true;
    }


    /**
     * Check If Match with Minimum Quantity in Cart
     * @return bool
     */
    protected function validateMinimumQuantity(): bool
    {
        if ($this->cartQty < $this->eventPromo->min_quantity) {
            $this->errors [] = 'coupon not fulfill with minimum ticket requirement';
            return false;
        }
        return true;
    }







}
