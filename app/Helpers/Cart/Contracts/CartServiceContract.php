<?php

namespace App\Helpers\Cart\Contracts;

use App\Models\Customer\Customer;
use Illuminate\Contracts\Auth\Authenticatable;

interface CartServiceContract
{

    public function getCustomer(): Customer|Authenticatable;

    public function getCouponCode(): ?string;

    public function hasChanged(): bool;

    public function setError(string $msg):void;

    public function getErrors():array;



}
