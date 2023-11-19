<div class="w-full h-full">


    <div class="w-full my-2 text-center">

        @if(is_null($rediectUrl))
{{--            Intent Process--}}
            <button  id="stripe-button1" class="bg-green-700 text-white text-lg md:text-xl py-1 rounded-xl w-full shadow-black shadow-lg">
                <i class="fas fa-money-bill"></i> Pay Via Stripe
            </button>
        @else
{{--            Checkout Process--}}
            <a href="{{$rediectUrl}}"  id="stripe-button1" class="bg-green-700 text-white text-lg md:text-xl py-1 px-3 rounded-xl w-full shadow-black shadow-lg">
                <i class="fas fa-money-bill"></i> Checkout with Stripe
            </a>
        @endif



    </div>



</div>














{{--@push('javascript')--}}
{{--    <script src="https://js.stripe.com/v3/"></script>--}}
{{--@endpush--}}

{{--@push('script')--}}
{{--    <script>--}}

{{--        const checkoutButton = document.getElementById('stripe-button1');--}}
{{--        checkoutButton.addEventListener('click',  function (event) {--}}
{{--            let stripe = Stripe('{{ config('services.stripe.pk_api_key') }}');--}}
{{--            const elements = stripe.elements({--}}
{{--                clientSecret: '{{ $payment['details']['client_secret'] }}',--}}
{{--                mode: 'payment',--}}
{{--                amount: {{$payment->total->getAmount()}}--}}
{{--            });--}}


{{--            elements.submit()--}}
{{--                .then(function(result) {--}}
{{--                    // Handle result.error--}}
{{--                });--}}

{{--            let paymentElement = elements.create('payment');--}}

{{--            paymentElement.update({business: {name: 'Stripe Shop'}});--}}


{{--        });--}}




{{--    </script>--}}














{{--@endpush--}}
