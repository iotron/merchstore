<div>

    @if($provider == \App\Models\Payment\PaymentProvider::RAZORPAY)
        <livewire:button.razorpay-button :payment="$payment"/>
    @elseif($provider == \App\Models\Payment\PaymentProvider::STRIPE)
        <livewire:button.stripe-button :payment="$payment"/>
    @endif


</div>

