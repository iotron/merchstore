<?php

namespace App\Services\OrderService\Supports\Order;

use Illuminate\Database\Eloquent\Model;

interface OrderBuilderContract
{

    public function model(?Model $model): static;
    public function items(array $items_array):static;

    public function receipt(string $receipt): static;

    public function bookingName(string $bookingName): static;
    public function bookingEmail(string $bookingEmail): static;

    public function bookingContact(int|string $bookingContact): static;

    public function cartMeta(array $cart_meta):static;

    public function getArray():array;






}
