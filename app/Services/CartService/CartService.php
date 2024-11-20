<?php

namespace App\Services\CartService;

use App\Models\Customer\Customer;
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
            'usages' => fn($query) => $query->where('customer_id',$this->customer->id)
        ])->firstWhere('code', $this->couponCode);
        if (!$this->isEmpty() && $this->couponModel) {
            if (CartCouponValidator::make($this->couponModel)->validate()) {
                $cartCalculatorService->setCoupon($this->validCoupon, $this->couponModel);
            }
        }

        $data = $cartCalculatorService->get();


        $data = [
            'subTotal' => new Money(0.00),
            'discount' => new Money(0.00),
            'tax' => new Money(0.00),
            'amount' => new Money(0.00),
            'products' => []
        ];

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
     * CURD OPERATIONS AND RELATED METHODS
     */



}
