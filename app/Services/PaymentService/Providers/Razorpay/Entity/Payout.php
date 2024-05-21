<?php

namespace App\Services\PaymentService\Providers\Razorpay\Entity;

use Razorpay\Api\Entity;

class Payout extends Entity
{
    public function fetch($id)
    {
        return parent::fetch($id);
    }

    public function all($options = [])
    {
        return parent::all($options);
    }

    public function create($attributes = [])
    {
        return parent::create($attributes);
    }

    /**
     * Cancel Queued Payouts
     *
     * @return \Payment\RazorpayApi\Payout|Entity
     */
    public function cancel($attributes = null)
    {
        $relativeUrl = $this->getEntityUrl().$this->id.'/cancel';

        return $this->request('POST', $relativeUrl);
    }
}
