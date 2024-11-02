<?php

namespace App\Services\PaymentService\Providers\Razorpay;

interface RazorpayPaymentServiceContract
{
    public function getSpeed(): string;

    public function getWebhookSecret(): string;

    public function getCompanyBankAccount(): string;

    public function payoutMode(): string;
}
