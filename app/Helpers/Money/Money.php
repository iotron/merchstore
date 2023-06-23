<?php

namespace App\Helpers\Money;

class Money extends MoneyService
{


    public function addOnce(Money|int|float$addend): self
    {
        $result = $this->laravelMoney->add(self::isMoney($addend) ? $addend->get() : $addend);
        return $this->createNewMoneyObject($result->getValue());
    }

    public function add(Money|int|float $addend): self
    {
        $this->laravelMoney = $this->laravelMoney->add(self::isMoney($addend) ? $addend->getAmount() : $addend);
        return $this;
    }

    public function subOnce(Money|int|float$subtrahend):self
    {
        $result = $this->laravelMoney->subtract(self::isMoney($subtrahend) ? $subtrahend->get() : $subtrahend);
        return $this->createNewMoneyObject($result->getValue());
    }


    public function subtract(Money|int|float$subtrahend):self
    {
        $this->laravelMoney = $this->laravelMoney->subtract(self::isMoney($subtrahend) ? $subtrahend->getAmount() : $subtrahend);
        return $this;
    }


    public function multiplyOnce(int|float $multiplier): self
    {
        $result = $this->laravelMoney->multiply($multiplier);
        return $this->createNewMoneyObject($result->getValue());
    }


    public function multiply(int|float $multiplier):self
    {
        $this->laravelMoney = $this->laravelMoney->multiply($multiplier);
        return $this;
    }



    public function divideOnce(int|float $divisor): self
    {
        $result = $this->laravelMoney->divide($divisor);
        return $this->createNewMoneyObject($result->getValue());
    }


    public function divide(int|float $divisor):self
    {
        $this->laravelMoney = $this->laravelMoney->divide($divisor);
        return $this;
    }






}
