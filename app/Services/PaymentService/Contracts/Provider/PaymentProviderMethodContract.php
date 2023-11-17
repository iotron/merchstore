<?php

namespace App\Services\PaymentService\Contracts\Provider;
interface PaymentProviderMethodContract
{


    public function create(array $data);

    public function fetch(string|int $id);

    public function all();



}
