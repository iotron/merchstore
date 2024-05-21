<?php

namespace App\Livewire\Button;

use App\Models\Payment\Payment;
use Livewire\Component;

class RazorpayButton extends Component
{
    public $options;

    protected Payment $payment;

    public bool $payable = false;

    public function mount(Payment $payment)
    {
        $payment->loadMissing('customer');

        $this->payment = $payment;

        $this->provider = $this->payment->provider->name;

        $this->options = $this->getProviderConfig();

        //dd($this->options);

        $this->payable = $this->payment->status == Payment::PENDING;

        // dd($this->options);

    }

    protected function getProviderConfig(): array
    {
        $paymentNotes = $this->payment->details['notes'];

        return [
            'key' => config('services.razorpay.api_key'),
            'amount' => $this->payment->total->getAmount(),
            'currency' => config('services.defaults.currency'),
            'name' => config('app.name'),
            //            'description' => 'Order Summary',
            // 'image' => '',
            'order_id' => $this->payment->provider_gen_id,
            'callback_url' => route('confirm.payment', ['payment' => $this->payment->receipt]),

            'prefill' => [
                'name' => $this->payment->customer->name,
                'email' => $this->payment->customer->email,
                'contact' => $this->payment->customer->contact,
            ],
            'notes' => $paymentNotes,
            'theme' => [
                'color' => '#410254',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.button.razorpay-button');
    }
}
