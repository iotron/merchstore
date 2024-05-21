<?php

namespace App\Livewire;

use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\View\Components\AppLayout;
use Illuminate\Http\Request;
use Livewire\Component;

class CheckoutPage extends Component
{
    protected Payment $payment;

    public $status;

    public int $timeout = 0;

    public bool $paymentExpire = true;

    public int $quantity = 0;

    public ?Order $order = null;

    public bool $payable = false;

    public function mount(Payment $payment, Request $request)
    {

        $payment->load('order');
        $this->payment = $payment;
        $this->status = $payment->status;
        $this->order = $payment->order;
        $this->quantity = $this->order->quantity;

        // Checks
        if ($this->payment->expire_at >= now()->toDateTimeString()) {
            $this->timeout = now()->diffInSeconds($this->payment->expire_at);
            $this->paymentExpire = false;
        }

        if ($this->payment->status != Payment::PENDING) {
            $this->paymentExpire = true;
        }
        $this->payable = $this->payment->status == Payment::PENDING;
        abort_unless($this->payable, 409, 'Conflict: This payment has already been made or payment is not payable anymore.');

    }

    public function returnClient()
    {
        return redirect()->to(config('app.client_url'));
    }

    public function render()
    {
        return view('livewire.checkout-page', [
            'payment' => $this->payment,
        ])->layout(AppLayout::class);
    }
}
