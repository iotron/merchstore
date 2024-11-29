<?php

namespace App\Livewire\Checkout;


use App\Models\Payment\Payment;
use App\View\Components\AppLayout;
use Livewire\Attributes\Locked;
use Livewire\Component;

class CheckoutPage extends Component
{
    protected Payment $payment;

    public $status;

    public int $timeout = 0;

    #[Locked]
    public string $failureUrl;

    #[Locked]
    public string $payBtn;

    public bool $paymentExpire = true;

    protected string $layout = 'filament-panels::components.layout.base';

    public function mount(Payment $payment)
    {
        //        dd($payment->load('bookable'));
        $payment->load('provider');
        $this->payment = $payment;
        $this->status = $payment->status;
        $this->payBtn = $this->payment->provider->url;

        if ($this->payment->expire_at >= now()->toDateTimeString()) {
            $this->timeout = now()->diffInSeconds($this->payment->expire_at);
            $this->paymentExpire = false;
        }

        if ($this->payment->verified) {
            $this->paymentExpire = true;
        }
        $this->failureUrl = $this->payment->failure_url;

      //  $this->paymentExpire = false;
    }

    public function returnBack()
    {
        return redirect()->to($this->failureUrl);
    }

    public function getRenderHookScopes()
    {

    }

    public function getTitle()
    {

    }

    public function getExtraBodyAttributes()
    {

    }

    public function getLayout(): ?string
    {
        return null;
    }

    public function render()
    {
        //AppLayout::class
        return view('livewire.checkout.checkout-page', [
            'payment' => $this->payment,
        ])->layout($this->getLayout() ?? AppLayout::class, ['livewire' => $this]);
    }
}
