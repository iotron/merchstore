<?php

namespace App\Services\ShippingService\Providers\ShipRocket\Actions;

use App\Services\ShippingService\Contracts\Provider\ShippingProviderCourierContract;
use App\Services\ShippingService\Providers\ShipRocket\ShipRocketApi;

class CourierAction implements ShippingProviderCourierContract
{


    protected ShipRocketApi $api;

    public function __construct(ShipRocketApi $api)
    {
        $this->api = $api;
    }

    public function all()
    {
        $response = $this->api->httpGet('courier/courierListWithCounts');
        return $response->json();
    }

    public function getCharge(int|string $pickup_postal, int|string $delivery_postal,array $data=[],int|bool $isCod=0)
    {
        $preparedQuery = 'pickup_postcode='.$pickup_postal.'&delivery_postcode='.$delivery_postal;
        // '&weight='.$weight.'&cod='.$isCod
        if (isset($data['weight']))
        {
            $preparedQuery .= '&weight='.$data['weight'];
        }
        if ($isCod)
        {
            $preparedQuery .= '&cod=1';
        }

        $response = $this->api->httpGet('courier/serviceability/',$preparedQuery);

        return $response->json();

    }


    public function fetch()
    {

    }















}
