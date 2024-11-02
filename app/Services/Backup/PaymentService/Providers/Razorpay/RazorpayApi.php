<?php

namespace App\Services\PaymentService\Providers\Razorpay;

use Razorpay\Api\Api;

class RazorpayApi extends Api
{
    protected const CUSTOM_ENTITY = ['contact', 'payout', 'fund_account'];

    public function __get($name)
    {
        if (in_array($name, self::CUSTOM_ENTITY)) {
            $className = __NAMESPACE__.'\\Entity\\'.ucwords($name);
            if (! class_exists($className)) {
                $className = __NAMESPACE__.'\\Entity\\'.str_replace('_', '', ucwords($name, '_'));
            }
            throw_unless(class_exists($className), 'class not found! '.$className);
            $entity = new $className();
        } else {
            $entity = parent::__get($name);
        }

        return $entity;

    }
}
