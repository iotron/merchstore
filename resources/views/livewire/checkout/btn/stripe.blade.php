<div>
    <button id="stripe-button1" class=" text-lg md:text-xl py-1 rounded-lg w-96 shadow-lg border-2 border-purple-500">
        <i class="fas fa-money-bill"></i>
        Pay Via Stripe
    </button>

    <div id="payment-element"></div>
</div>





@assets
<script src="https://js.stripe.com/v3/"></script>
@endassets


@push('script')

        <script>

            let options = @js($configuration);
            const checkoutButton = document.getElementById('stripe-button1');
            checkoutButton.addEventListener('click', function (event) {

                console.log(options.active_mode);
                if(options.active_mode == 'checkout'){
                    window.location.href = options.url;
                }else{

                    let stripe = Stripe(options.api_key);
                    let elements = stripe.elements();
                    let card = elements.create('card', {
                        hidePostalCode: true
                    });
                    card.mount('#payment-element');
                }


            });



        </script>

@endpush
