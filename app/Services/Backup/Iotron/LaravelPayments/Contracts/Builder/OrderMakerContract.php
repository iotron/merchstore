<?php

namespace App\Services\Iotron\LaravelPayments\Contracts\Builder;

interface OrderMakerContract
{
    public static function make(): static;

    public function currency(string $currency_code): static;

    public function receipt(string $receipt): static;

    public function buyerName(string $buyerName): static;

    public function buyerEmail(string $buyerEmail): static;

    public function buyerContact(int|string $buyerContact): static;

    public function items(array $items): static;

    public function name(string $name): static;

    public function callbackUrl(string $callback_url): static;

    public function successUrl(string $success_url): static;

    public function failureUrl(string $failure_url): static;

    public function builder(OrderBuilderContract $orderBuilder): static;

    public function toArray(): array;
}
