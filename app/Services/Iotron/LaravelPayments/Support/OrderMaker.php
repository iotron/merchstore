<?php

namespace App\Services\Iotron\LaravelPayments\Support;

use App\Services\Iotron\LaravelPayments\Contracts\Builder\OrderBuilderContract;
use App\Services\Iotron\LaravelPayments\Contracts\Builder\OrderMakerContract;
use Throwable;

class OrderMaker implements OrderMakerContract
{
    protected array $cartMeta = [];

    protected string $receipt = '';

    protected string $buyerName = '';

    protected string $buyerEmail = '';

    protected string|int $buyerContact = '';

    protected array $items = [];

    protected string $callbackUrl = '';

    protected string $successUrl = '';

    protected string $failureUrl = '';

    protected ?OrderBuilderContract $builder = null;

    protected string $currency = 'usd';

    protected string|int|float $totalAmount = 0;

    protected ?string $name = null;

    public static function make(): static
    {
        return new static();
    }

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

    public function name(string $name): static
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

    public function builder(OrderBuilderContract $orderBuilder): static
    {
        $this->builder = $orderBuilder;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function toArray(): array
    {
        // throw_unless($this->builder,'Order Builder Not Set');
        if ($this->builder) {
            return $this->builder
                ->currency($this->currency)
                ->total($this->totalAmount)
                ->receipt($this->receipt)
                ->buyerEmail($this->buyerEmail)
                ->buyerContact($this->buyerContact)
                ->buyerName($this->buyerName)
                ->name($this->name)
                ->items($this->items)
                ->callbackUrl($this->callbackUrl)
                ->successUrl($this->successUrl)
                ->failureUrl($this->failureUrl)
                ->toArray();
        } else {
            return [
                'provider' => [
                    'receipt' => $this->receipt,
                    'currency' => $this->currency,
                    'amount' => $this->totalAmount,
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
}
