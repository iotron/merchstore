<?php

namespace App\Services\ShippingService\Providers\ShipRocket;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
class ShipRocketApi
{

    protected string $apiBase = 'https://apiv2.shiprocket.in/v1/external/';
    protected string $apiUrl =  'https://apiv2.shiprocket.in/v1/external/auth/login';
    protected string $apiToken;


    public function __construct(string $api_key,string $api_secret)
    {

        $this->apiToken = Http::post($this->apiUrl, [
            'email' => $api_key,
            'password' => $api_secret,
        ])['token'];
    }

    public function http(): PendingRequest
    {
        return Http::withToken($this->apiToken)->contentType('application/json');
    }

    public function getToken():string
    {
        return $this->apiToken;
    }

    public function getBaseUrl(): string
    {
        return $this->apiBase;
    }





}
