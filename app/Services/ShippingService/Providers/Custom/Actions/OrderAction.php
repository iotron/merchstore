<?php

namespace App\Services\ShippingService\Providers\Custom\Actions;

use App\Services\ShippingService\Contracts\Provider\ShippingProviderActionContract;

class OrderAction implements ShippingProviderActionContract
{

    public function create(array $data)
    {

        $added = [
            "order_id" => 16161616,
            "shipment_id" => 15151515,
              "status" => "NEW",
              "status_code" => 1,
              "onboarding_completed_now" => 0,
              "awb_code" => null,
              "courier_company_id" => null,
              "courier_name" => null
        ];


        return array_merge($added,$data);
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
