<?php

namespace App\Livewire;

use App\Models\Payment\Payment;
use Livewire\Component;

class PayButton extends Component
{

//    public $id;
    protected Payment $payment;
    public $provider;
    public bool $payable = false;



    public function mount(Payment $payment)
    {

        $this->payment = $payment->load('provider');

        $this->provider = $this->payment->provider->url;



        $this->payable = $this->payment->status == Payment::PENDING;


    }







    public function render()
    {
        return view('livewire.pay-button',[
            'payment' => $this->payment
        ]);
    }


}
