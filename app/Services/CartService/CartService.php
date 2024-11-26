<?php

namespace App\Services\CartService;

use App\Models\Customer\Customer;
use App\Models\Product\Product;
use App\Models\Promotion\VoucherCode;
use App\Services\CartService\Support\CartCalculationService;
use App\Services\CartService\Support\CartCouponValidator;
use App\Services\MoneyServices\Money;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;

class CartService
{


    protected Customer|Authenticatable $customer;
    protected ?string $couponCode = null;
    protected bool $changed = false;
    protected array $errors = [];
    protected int $totalQuantity = 0;
    protected bool $validCoupon = false;
    protected ?VoucherCode $couponModel = null;
    public bool $requestForReLoadCustomerCartInRuntime = false;

    public function __construct(Customer|Authenticatable $customer, ?string $couponCode = null)
    {
        $this->customer = $customer;
        $this->couponCode = $couponCode;
        $this->customer->loadMissing('cart');
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

    public function setError(string $msg): void
    {
        $this->errors[] = $msg;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }



    public function isEmpty(): bool
    {
        return $this->customer->cart->sum('pivot.quantity') === 0;
    }

    public function products(): Collection
    {
        if (App::runningInConsole() || $this->requestForReLoadCustomerCartInRuntime) {
            return  $this->customer->fresh()->cart;
        }
        return $this->customer->cart;
    }
    public function getTotalQuantity(): int
    {

        $this->totalQuantity = $this->products()->sum('pivot.quantity');

        return $this->totalQuantity;
    }


    /**
     * Get Cart Details
     * @return array
     */

    public function getCalculatedData():array
    {
        $cartCalculatorService = CartCalculationService::make()->items($this->products());
        $this->couponModel = VoucherCode::with([
            'voucher',
            'voucher.customer_groups' => fn($query) => $query->where('customer_group_id', $this->customer->customer_group_id),
            'usages' => fn($query) => $query->where('customer_id',$this->customer->id)
        ])->firstWhere('code', $this->couponCode);
        if (!$this->isEmpty() && $this->couponModel) {
            $couponValidator = CartCouponValidator::make($this->couponModel);
            if ($couponValidator->validate($this->getTotalQuantity())) {
                $cartCalculatorService->setCoupon($this->validCoupon, $this->couponModel);
            }else{
                $this->errors = array_merge($this->errors,$couponValidator->getErrors());
            }
        }

        $data = [
            'subTotal' => new Money(0.00),
            'discount' => new Money(0.00),
            'tax' => new Money(0.00),
            'amount' => new Money(0.00),
            'totalQuantity' => $this->getTotalQuantity(),
            'products' => []
        ];

        if (empty($this->errors))
        {
            $data = $cartCalculatorService->get($data);
        }


        return array_merge([
            'coupon' => $this->couponCode,
            'validCoupon' => $this->validCoupon,
            'couponModel' => $this->couponModel,
            'empty' => $this->isEmpty(),
            'changed' => $this->changed,
            'customer' => $this->getCustomer()->email,
            'quantity' => $this->getTotalQuantity(),
            'error' => $this->getErrors(),
        ], $data);


    }





    /**
     * Coupon
     * CURD OPERATIONS AND RELATED METHODS
     */

    public function addCoupon(string $code)
    {
        $this->couponModel = VoucherCode::with([
            'voucher',
            'voucher.customer_groups' => fn($query) => $query->where('customer_group_id', $this->customer->customer_group_id),
            'usages' => fn($query) => $query->where('customer_id',$this->customer->id)
        ])->firstWhere('code', $code);
        $couponValidator = CartCouponValidator::make($this->couponModel);
        if ($this->couponModel && $couponValidator->validate())
        {
            $this->couponCode = $code;
            $this->validCoupon = true;
            session(['coupon' => $code]);
        }else{
            $this->errors = array_merge($this->errors,$couponValidator->getErrors());
        }

    }

    public function removeCoupon(string $code)
    {
        if ($this->couponCode === $code) {
            session()->forget('coupon');
            $this->couponCode = null;
            $this->validCoupon = false;
        }
    }



    /**
     * Products
     * CURD OPERATIONS AND RELATED METHODS
     */


    public function add(int $itemId, int $quantity):void
    {
        // Exist Item Handel In Controller, And we got product id here, not sku
        // So We do only fresh insert record
        $selectedItem = Product::firstWhere('id',$itemId);
        if ($selectedItem->max_range >= $quantity && $selectedItem->min_range <= $quantity)
        {
            // Fresh Add in Cart
            $this->customer->cart()->attach($selectedItem->id, ['quantity' => $quantity]);
        }else{
            $this->customer->cart()->attach($selectedItem->id, ['quantity' => $selectedItem->max_range]);
        }
    }


    public function update(int $itemID, int $quantity): void
    {
        $this->customer->cart()->updateExistingPivot($itemID, [
            'quantity' => $quantity,
        ]);
    }

    public function delete(int $itemID): void
    {

        if ($this->products()->contains('id', $itemID)) {
            $this->customer->cart()->detach($itemID);

        } else {
            $this->errors[] = 'product not found!';
        }
    }




}
