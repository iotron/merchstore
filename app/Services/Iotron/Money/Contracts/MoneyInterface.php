<?php

namespace App\Services\Iotron\Money\Contracts;

use App\Services\Iotron\Money\Money;

interface MoneyInterface
{
    public function addOnce(Money|int|float $addend): self;

    public function add(Money|int|float $addend): self;

    public function subOnce(Money|int|float $subtrahend): self;

    public function subtract(Money|int|float $subtrahend): self;

    public function multiplyOnce(int|float $multiplier): self;

    public function multiply(int|float $multiplier): self;

    public function divideOnce(int|float $divisor): self;

    public function divide(int|float $divisor): self;
}
