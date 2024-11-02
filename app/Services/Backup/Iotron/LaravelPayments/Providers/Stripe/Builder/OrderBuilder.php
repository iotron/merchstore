<?php

namespace App\Services\Iotron\LaravelPayments\Providers\Stripe\Builder;

use App\Services\Iotron\LaravelPayments\Contracts\Builder\OrderBuilderContract;

class OrderBuilder implements OrderBuilderContract
{
    protected array $cartMeta = [];

    protected string $receipt;

    protected string $buyerName;

    protected string $buyerEmail;

    protected string|int $buyerContact;

    protected array $items = [];

    protected string $name = 'Bill Amount';

    protected string $callbackUrl;

    protected string $successUrl;

    protected string $failureUrl;

    protected string $currency = 'usd';

    protected string|int|float $totalAmount = 0;

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

        foreach ($items as $item) {

            $this->items[] = [
                'price_data' => [
                    'currency' => $this->currency,
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                    'unit_amount' => $item['net_amount_per_unit'],
                ],
                'quantity' => $item['pivot_quantity'],
            ];
        }

        return $this;
    }

    public function name(?string $name = null): static
    {
        $this->name = $name ?? 'Bill Amount';

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
        $defaultArray = [
            'line_items' => ! empty($this->items) ? $this->items : [
                [
                    'price_data' => [
                        'currency' => $this->currency,
                        'product_data' => [
                            'name' => $this->name,
                        ],
                        'unit_amount' => $this->totalAmount,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'customer_email' => $this->buyerEmail,
            'currency' => $this->currency,
        ];
        if (config('laravel-payments.config.stripe.self_hosted')) {
            $hosted = [
                'return_url' => $this->callbackUrl,
                'ui_mode' => 'embedded',
            ];
        } else {
            $hosted = [
                'cancel_url' => $this->failureUrl,
                'success_url' => $this->callbackUrl.'?session_id={CHECKOUT_SESSION_ID}',
            ];
        }

        return [
            'provider' => array_merge($defaultArray, $hosted),
            'additional' => [
                'currency' => $this->currency,
                'totalAmount' => $this->totalAmount,
                'receipt' => $this->receipt,
                'buyer_email' => $this->buyerEmail,
                'buyer_name' => $this->buyerName,
                'buyer_contact' => $this->buyerContact,
                'items' => $this->items,
                'callback_url' => $this->callbackUrl,
                'success_url' => $this->successUrl,
                'failure_url' => $this->failureUrl,
            ],
        ];
    }
}
