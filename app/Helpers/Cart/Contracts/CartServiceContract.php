<?php

namespace App\Helpers\Cart\Contracts;

use App\Models\Customer\Customer;
use App\Models\Promotion\VoucherCode;
use App\Services\Iotron\MoneyService\Money;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

interface CartServiceContract
{
    public function getCustomer(): Customer|Authenticatable;

    public function getCouponCode(): ?string;

    public function getCouponModel(): ?VoucherCode;

    public function hasChanged(): bool;

    public function setError(string $msg): void;

    public function getErrors(): array;

    public function setSubTotal(Money $subTotal);

    public function getAttribute($attribute);

    public function setAttribute(int|string $attribute, mixed $data): void;

    public function getProduct();

    //    public function setCouponStatus(bool $status):void;
    //    public function getCouponStatus(): bool;
    //
    public function addCoupon(string $code): void;

    public function removeCoupon(string $code): bool;

    public function addBulk(array $product): void;

    public function add(int $itemID, int $quantity): void;

    public function update(int $itemID, int $quantity): void;

    public function delete(int $itemID): void;

    public function empty(): void;

    public function isEmpty(): bool;

    public function getTotalQuantity(): int;

    public function products(): Collection;

    public function checkStock(): void;
}
