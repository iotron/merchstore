<?php

namespace App\Services\ShippingService\Contracts\Provider;

interface ShippingProviderActionContract
{

    public function create(array $data);

    public function all();

    public function fetch(int|string $id);

    public function verify():bool;


}
