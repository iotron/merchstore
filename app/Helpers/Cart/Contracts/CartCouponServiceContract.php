<?php

namespace App\Helpers\Cart\Contracts;

use Illuminate\Database\Eloquent\Model;

interface CartCouponServiceContract
{
    public function setCode(string $couponCode): void;

    public function getCode(): ?string;

    public function isValid(): bool;

    public function getModel(): ?Model;

    public function validated(?string $coupon_code): bool;

    public function destroy(): void;
}
