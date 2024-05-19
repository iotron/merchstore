<?php

namespace App\Services\Iotron\LaravelPayments\Providers\Razorpay\Builder;

use App\Services\Iotron\LaravelPayments\Contracts\Builder\OrderBuilderContract;

class OrderBuilder implements OrderBuilderContract
{
    protected array $cartMeta = [];

    protected string $receipt;

    protected string $buyerName;

    protected string $buyerEmail;

    protected string|int $buyerContact;

    protected array $items;

    protected string $callbackUrl;

    protected string $successUrl;

    protected string $failureUrl;

    protected string $currency = 'usd';

    protected string|int|float $totalAmount = 0;

    protected ?string $name = null;

    public function currency(string $currency_code): static
    {
        $this->currency = $currency_code;

        return $this;
    }

    public function total(int|float|string $total_amount): static
    {
        $this->totalAmount = $total_amount;

        return $this;
    }

    public function receipt(string $receipt): static
    {
        $this->receipt = $receipt;

        return $this;
    }

    public function buyerName(string $buyerName): static
    {
        $this->buyerName = $buyerName;

        return $this;
    }

    public function buyerEmail(string $buyerEmail): static
    {
        $this->buyerEmail = $buyerEmail;

        return $this;
    }

    public function buyerContact(int|string $buyerContact): static
    {
        $this->buyerContact = $buyerContact;

        return $this;
    }

    public function items(array $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function name(?string $name = null): static
    {
        $this->name = $name;

        return $this;
    }

    public function callbackUrl(string $callback_url): static
    {
        $this->callbackUrl = $callback_url;

        return $this;
    }

    public function successUrl(string $success_url): static
    {
        $this->successUrl = $success_url;

        return $this;
    }

    public function failureUrl(string $failure_url): static
    {
        $this->failureUrl = $failure_url;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'provider' => [
                'receipt' => $this->receipt,
                'amount' => $this->totalAmount,
                'currency' => $this->currency,
            ],

            'additional' => [
                'currency' => $this->currency,
                'totalAmount' => $this->totalAmount,
                'receipt' => $this->receipt,
                'buyer_email' => $this->buyerEmail,
                'buyer_name' => $this->buyerName,
                'buyer_contact' => $this->buyerContact,
                'callback_url' => $this->callbackUrl,
                'success_url' => $this->successUrl,
                'failure_url' => $this->failureUrl,
            ],
        ];
    }
}
