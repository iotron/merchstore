<?php

namespace App\Helpers\Cart\Services;

use App\Helpers\Cart\Contracts\CartServiceContract;
use App\Models\Customer\Customer;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;

abstract class CartService implements CartServiceContract
{

    protected Customer|Authenticatable $customer;
    protected bool $changed = false;
    protected array $errors = [];
    protected ?string $couponCode = null;

    public function __construct(Customer|Authenticatable $customer, ?string $couponCode = null)
    {
        $this->customer = $customer;
        $this->couponCode = $couponCode;

    }


    public function getCustomer(): Customer|Authenticatable
    {
        return $this->customer;
    }

    public function getCouponCode(): ?string
    {
        return $this->couponCode;
    }

    public function hasChanged(): bool
    {
        return $this->changed;
    }

    public function setError(string $msg):void
    {
        $this->errors[] = $msg;
    }

    public function getErrors():array
    {
        return $this->errors;
    }

    public function empty(): void
    {
        $this->customer->cart()->detach();
    }

    public function isEmpty(): bool
    {
        return $this->customer->cart->sum('pivot.quantity') === 0;
    }

    public function products():Collection
    {
        if (App::runningInConsole()) {
            if ($this->customer->cart->count())
            {
                return $this->customer->cart;
            }else{
                return $this->customer->fresh()->cart;
            }
        }
        return $this->customer->cart;
    }


    /**
     * Cart CURD Methods
     */


    public function add(int $itemID, int $quantity): void
    {

        $existIDs = $this->customer->cart->pluck('id')->toArray();
        if (in_array($itemID,$existIDs))
        {
            // Update Exist Product/Item Id Quantity In Cart
            $this->update($itemID,$quantity);

        }else{
            // Fresh Add in Cart
            $this->customer->cart()->attach($itemID,['quantity' => $quantity]);
        }
    }

    public function update(int $itemID, int $quantity): void
    {
        $this->customer->cart()->updateExistingPivot($itemID,[
            'quantity' => $quantity
        ]);
    }



}
