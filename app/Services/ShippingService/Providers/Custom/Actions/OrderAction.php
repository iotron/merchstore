<?php

namespace App\Services\ShippingService\Providers\Custom\Actions;

use App\Services\ShippingService\Contracts\Provider\ShippingProviderActionContract;

class OrderAction implements ShippingProviderActionContract
{

    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    public function all()
    {
        // TODO: Implement all() method.
    }

    public function fetch(int|string $id)
    {
        // TODO: Implement fetch() method.
    }

    public function verify(): bool
    {
        // TODO: Implement verify() method.
    }
}
