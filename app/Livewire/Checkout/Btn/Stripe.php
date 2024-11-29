<?php

namespace App\Livewire\Checkout\Btn;


use App\Models\Payment\Payment;
use Livewire\Component;

class Stripe extends Component
{
    protected Payment $payment;

    public bool $payable = false;

    public array $configuration;

    public function mount(Payment $payment)
    {
        $this->payment = $payment;
        $this->payable = !$this->payment->verified;
        $this->configuration = $this->getProviderConfig();

    }

    public function render()
    {
        return view('livewire.checkout.btn.stripe');
    }

    protected function getProviderConfig()
    {
        //        dd($this->payment->details);
        return [
            'api_key' => config('services.stripe.pk_api_key'),
            'secret' => config('services.stripe.secret'),
            'mode' => $this->payment->details['provider']['mode'],
            'amount' => $this->payment->amount,
            'client_secret' => $this->payment->details['provider']['client_secret'],
            'url' => $this->payment->details['provider']['url'],
            'active_mode' => config('payment-provider.providers.stripe.mode'),
        ];
    }
}
