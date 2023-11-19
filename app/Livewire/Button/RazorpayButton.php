<?php

namespace App\Livewire\Button;

use App\Models\Payment\Payment;
use App\Models\Payment\PaymentProvider;
use Livewire\Component;

class RazorpayButton extends Component
{

    public $options;
    protected Payment $payment;
    public bool $payable = false;

    public function mount(Payment $payment)
    {

        $this->payment = $payment;

        $this->provider = $this->payment->provider->name;

        $this->options = $this->getProviderConfig();


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
            'description' => 'Order Summary',
            // 'image' => '',
            'order_id' => $this->payment->provider_gen_id,
            'callback_url' => route('confirm.payment', ['payment' => $this->payment->receipt]),

            'prefill' => [
                'name' => $paymentNotes['booking_name'],
                'email' => $paymentNotes['booking_email'],
                'contact' => $paymentNotes['booking_contact']
            ],
            'notes' => $paymentNotes,
            'theme' => [
                "color" => "#410254",
            ],
        ];
    }






    public function render()
    {
        return view('livewire.button.razorpay-button');
    }
}
