<?php

namespace App\Livewire;

use App\Models\Payment\Payment;
use Livewire\Component;

class PayButton extends Component
{

//    public $id;
    protected Payment $payment;
    public string $provider;
    public bool $payable = false;



    public function mount(Payment $payment)
    {
        $this->payment = $payment->load('provider');
        $this->provider = $this->payment->provider->code;
        $this->payable = $this->payment->status == Payment::PENDING;
      //  dd($this->payment);
    }







    public function render()
    {
        return view('livewire.pay-button',[
            'payment' => $this->payment
        ]);
    }


}
