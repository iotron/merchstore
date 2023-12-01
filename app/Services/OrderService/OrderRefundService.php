<?php

namespace App\Services\OrderService;

use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\Models\Payment\Refund;
use App\Services\PaymentService\Contracts\PaymentProviderContract;
use App\Services\PaymentService\PaymentService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class OrderRefundService
{

    protected ?string $error = null;
    private Order|Model $order;
    private PaymentService $paymentService;
    protected ?Payment $payment;
    protected PaymentProviderContract $paymentProvider;

    public function __construct(Order|Model $order, PaymentService $paymentService)
    {
        $this->order = $order;
        $this->paymentService = $paymentService;
        $this->payment = Payment::with('provider')->firstWhere('order_id',$this->order->id);
        $this->paymentProvider = $this->paymentService->provider($this->payment->provider->code)->getProvider();
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function refund():bool
    {
        if ($this->order->refund()->count())
        {
            $this->error = 'Refund Already Made!';
            Notification::make()
                ->danger()
                ->title('Refund Not Possible!')
                ->send();
        }else{
            // Do the Refund
           $response =  $this->paymentProvider->refund()->create($this->payment);

           dd($response);

            if ($response['status'] == 'processed')
            {
                // Update Payment
                $this->payment->fill([
                    'status' => Payment::REFUND
                ])->save();
                // Create And Return Refund Model
                return $this->order->refund()->create([
                    'refund_id' => $response['id'],
                    'amount' => $response['amount'],
                    'currency' => $response['currency'],
                    'payment_id' => $response['payment_id'],
                    'receipt' => $this->order->payment->receipt,
                    'speed' => $response['speed_processed'],
                    'status' => Refund::COMPLETED,
                    'batch_id' => $response['batch_id'],
                    'notes' => $response['notes']->toArray(),
                    'tracking_data' =>  $response['acquirer_data']->toArray(),
                    'details' => $response->toArray(),
                    'error' => $response['error'] ?? null,
                ]);
            }

        }


        return is_null($this->error);
    }


}
