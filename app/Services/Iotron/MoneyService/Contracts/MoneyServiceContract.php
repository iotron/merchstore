<?php

namespace App\Services\Iotron\MoneyService\Contracts;

use Akaunting\Money\Currency;
use Akaunting\Money\Money as LaravelMoney;

interface MoneyServiceContract
{
    public function sameAs($value, ?string $currency = null): bool;

    public function compare($value, ?string $currency = null): int;

    public function currency(): Currency;

    public function getCurrency(): Currency;

    public function getCurrencyCode(): string;

    public function amount(): float|int;

    public function getAmount(): float|int;

    public function formatted(): string;

    public function forHuman(): string;

    public function getValue(): float;

    public function get(): LaravelMoney;

    public static function resolveCurrency(?string $currency = null): Currency;

    public static function format(int|float|string $value, ?string $currency = null): string;

    public static function isMoney($object): bool;
}
