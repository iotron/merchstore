<?php

namespace App\Services\ShippingService\Providers\ShipRocket;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
class ShipRocketApi
{

    protected string $apiBase = 'https://apiv2.shiprocket.in/v1/external/';
    protected string $token;


    public function __construct(string $email,string $password)
    {
        $loginData = $this->login([
            'email' => $email,
            'password' => $password
        ]);
        throw_unless($loginData['token'],'Shiprocket Authentication Failed! check credentials');
        $this->token = $loginData['token'];
    }

    public function getToken():string
    {
        return $this->token;
    }

    public function getBaseUrl(): string
    {
        return $this->apiBase;
    }

    protected function login(array $credentials)
    {
        $response = Http::post($this->getBaseUrl()."auth/login", [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ]);
        throw_if($response->failed(),$response->body());
        return $response->json();
    }





    public function http(): PendingRequest
    {
        return Http::withToken($this->token)->retry(3, 100);
        //return Http::withToken($this->apiToken)->contentType('application/json');
    }







}
