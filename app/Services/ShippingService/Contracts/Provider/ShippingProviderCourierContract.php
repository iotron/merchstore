<?php

namespace App\Services\ShippingService\Contracts\Provider;

interface ShippingProviderCourierContract
{

    public function all();
    public function getCharge(int|string $pickup_postal, int|string $delivery_postal,array $data=[],int|bool $isCod=0);

    public function fetch();

}
