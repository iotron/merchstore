<?php

namespace App\Services\ShippingService\Providers\Custom\Actions;

use App\Services\ShippingService\Contracts\Provider\ShippingProviderCourierContract;

class CourierAction implements ShippingProviderCourierContract
{

    /**
     * @return mixed
     */
    public function all()
    {
        // TODO: Implement all() method.
    }

    /**
     * @param int|string $pickup_postal
     * @param int|string $delivery_postal
     * @param int $weight
     * @param int|bool $isCod
     * @return mixed
     */
    public function getCharge(int|string $pickup_postal, int|string $delivery_postal, array $data=[], bool|int $isCod = 0)
    {
        // TODO: Implement getCharge() method.
    }

    /**
     * @return mixed
     */
    public function fetch()
    {
        // TODO: Implement fetch() method.
    }
}
