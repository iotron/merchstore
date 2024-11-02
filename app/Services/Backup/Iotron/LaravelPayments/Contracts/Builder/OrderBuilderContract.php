<?php

namespace App\Services\Iotron\LaravelPayments\Contracts\Builder;

interface OrderBuilderContract
{
    public function currency(string $currency_code): static;

    public function total(int|float|string $total_amount): static;

    public function receipt(string $receipt): static;

    public function buyerName(string $buyerName): static;

    public function buyerEmail(string $buyerEmail): static;

    public function buyerContact(int|string $buyerContact): static;

    public function items(array $items): static;

    public function name(?string $name = null): static;

    public function callbackUrl(string $callback_url): static;

    public function successUrl(string $success_url): static;

    public function failureUrl(string $failure_url): static;

    public function toArray(): array;
}
