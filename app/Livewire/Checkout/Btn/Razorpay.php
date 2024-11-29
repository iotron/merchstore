<?php

namespace App\Livewire\Checkout\Btn;


use App\Models\Payment\Payment;
use Livewire\Component;

class Razorpay extends Component
{
    protected Payment $payment;

    public bool $payable = false;

    public array $configuration;

    public $failureUrl;

    public function mount(Payment $payment)
    {
        $payment->loadMissing('bookable');
        $this->payment = $payment;
        $this->payable = !$this->payment->verified;
        $this->configuration = $this->getProviderConfig();
        $this->failureUrl = $this->payment->failure_url;


    }

    protected function getProviderConfig(): array
    {

        $bookingModel = $this->payment->bookable;
        $isBusinessBooking = $bookingModel instanceof \App\Models\Location\BusinessBooking;

        // need to fix relation bookedBy
        $bookedUser = $isBusinessBooking ? $bookingModel->bookedBy : null;

        return [
            'key' => config('services.razorpay.api_key'),
            'amount' => $this->payment->amount,

            'currency' => $this->payment->details['additional']['currency'],
            'name' => config('app.name'),
            'description' => 'Order Summary',
            'image' => '',
            'order_id' => $this->payment->provider_gen_id,
            'callback_url' => $this->payment->callback_url,

            'prefill' => [
                'name' => $this->payment->details['additional']['buyer_name'],
                'email' => $this->payment->details['additional']['buyer_email'],
                'contact' => $this->payment->details['additional']['buyer_contact'],
            ],
            'theme' => [
                'color' => '#410254',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.checkout.btn.razorpay');
    }
}
