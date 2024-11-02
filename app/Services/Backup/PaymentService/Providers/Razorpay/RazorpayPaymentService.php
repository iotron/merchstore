<?php

namespace App\Services\PaymentService\Providers\Razorpay;

use App\Models\Payment\PaymentProvider;
use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderMethodContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderOrderContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderPayoutContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderRefundContract;
use App\Services\PaymentService\Contracts\Provider\PaymentProviderVerificationContract;
use App\Services\PaymentService\Providers\Razorpay\Actions\ContactAction;
use App\Services\PaymentService\Providers\Razorpay\Actions\FundAccountAction;
use App\Services\PaymentService\Providers\Razorpay\Actions\OrderAction;
use App\Services\PaymentService\Providers\Razorpay\Actions\PayoutAction;
use App\Services\PaymentService\Providers\Razorpay\Actions\RefundAction;
use App\Services\PaymentService\Providers\Razorpay\Actions\VerifyAction;

class RazorpayPaymentService implements PaymentProviderContract, RazorpayPaymentServiceContract
{
    private RazorpayApi $api;

    private ?string $error = null;

    protected string $speed = 'normal';

    private ?RazorpayApi $apiX = null;

    protected bool $razorpayX = false;

    protected ?PaymentProvider $providerModel = null;

    public function __construct(?PaymentProvider $providerModel, RazorpayApi $api, bool $activateX = false)
    {

        $this->providerModel = $providerModel;
        $this->api = $api;
        $this->razorpayX = $activateX;
        $this->discoverConfig();

    }

    protected function discoverConfig()
    {
        $this->speed = config('payment-provider.providers.razorpay.speed');
        if ($this->razorpayX) {
            throw_if(empty(config('services.razorpay.api_x_key')), 'Razorpay-X Key not found!', 500);
            throw_if(empty(config('services.razorpay.api_x_secret')), 'Razorpay-X Secret not found!', 500);

            $this->apiX = $this->getRazorpayXApi();
        }
    }

    public function getSpeed(): string
    {
        return $this->speed;
    }

    public function getModel(): ?PaymentProvider
    {
        return $this->providerModel;
    }

    public function order(): PaymentProviderOrderContract
    {
        return new OrderAction($this->api, $this);
    }

    public function payment(): PaymentProviderMethodContract
    {
        // TODO: Implement payment() method.
    }

    public function verify(): PaymentProviderVerificationContract
    {
        return new VerifyAction($this->api, $this);
    }

    public function refund(): PaymentProviderRefundContract
    {
        return new RefundAction($this->api, $this);
    }

    public function payout(): PaymentProviderPayoutContract
    {
        return new PayoutAction($this->apiX, $this);
    }

    public function contact()
    {
        return new ContactAction($this->apiX, $this);
    }

    public function fundAccount()
    {
        return new FundAccountAction($this->apiX, $this);
    }

    public function getApi(): object
    {
        return $this->api;
    }

    private function getRazorpayXApi(): RazorpayApi
    {
        return new RazorpayApi(config('services.razorpay.api_x_key'), config('services.razorpay.api_x_secret'));
    }

    public function getProviderName(): string
    {
        return 'razorpay';
    }

    public function getClass(): string
    {
        return get_class($this);
    }

    public function getProvider(): static|PaymentProviderContract
    {
        return $this;
    }

    public function setError(string $error): void
    {
        $this->error = $error;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getWebhookSecret(): string
    {
        return config('services.razorpay.webhook_secret');
    }

    public function getCompanyBankAccount(): string
    {
        return config('services.razorpay.payout.account_no');
    }

    public function payoutMode(): string
    {
        return config('services.razorpay.payout.mode');
    }
}
