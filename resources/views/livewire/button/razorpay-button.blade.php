<div class="w-full my-2">
    <button id="rzp-button1" class="bg-green-700 text-white text-lg md:text-xl py-1 rounded-xl w-full shadow-black shadow-lg"><i
            class="fas fa-money-bill"></i>
{{--        Pay Now--}}
        Pay Via Razorpay
    </button>
</div>

@push('javascript')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
@endpush

@push('script')

    <script>

        function getPaymentInstance() {
            let options = @js($options);
            console.log(options);
            options.modal = {
                ondismiss: function () {
                    if (confirm("Are you sure you want to close the form?")) {
                        txt = "You pressed OK!";
                        console.log("Checkout form closed by the user");
                    } else {
                        txt = "You pressed Cancel!";
                        console.log("Complete the Payment");
                    }
                }
            };
            return new Razorpay(options);
        }


        document.getElementById('rzp-button1').onclick = function (e) {

            let rzp1 = getPaymentInstance();
            rzp1.open();
            console.log(e);
            e.preventDefault();
        }

        window.addEventListener("load", (event) => {
            let isPending = @js($payable);
            if (isPending) {
                let rzp1 = getPaymentInstance();
                rzp1.open();
                console.log(e);
                e.preventDefault();
            }
        });


    </script>

@endpush






