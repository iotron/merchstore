<?php

namespace App\Livewire\Button;

use App\Models\Payment\Payment;
use App\Models\Payment\PaymentProvider;
use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\PaymentService;
use App\Services\PaymentService\Providers\Stripe\StripePaymentServiceContract;
use Livewire\Component;
use Stripe\Stripe;

class StripeButton extends Component
{




    public $options;
    protected Payment $payment;
    public bool $payable = false;

    public bool $showPaymentModal=false;
    public ?string $rediectUrl = null;
    private PaymentProviderContract|StripePaymentServiceContract $provider;
    private bool $intentPayment = false;
    private bool $checkoutPayment = false;

    public function mount(Payment $payment,PaymentService $paymentService)
    {


        $this->payment = $payment;
//
//        dd($payment->details);

        $this->provider = $paymentService->provider('stripe');

        $this->intentPayment = $this->provider->isIntent();
        $this->checkoutPayment = $this->provider->isCheckout();

        $this->payable = $this->payment->status == Payment::PENDING;
        if ($this->checkoutPayment)
        {

            $this->rediectUrl = $this->payment->details['url'];
        }


    }





    public function render()
    {
        return view('livewire.button.stripe-button',[
            'payment' => $this->payment
        ]);
    }

}
