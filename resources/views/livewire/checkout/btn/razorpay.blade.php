<div>
    <button id="rzp-button1" class=" text-lg md:text-xl py-1 rounded-lg w-96 shadow-lg border-2 border-purple-500">
        <i class="fas fa-money-bill"></i>
        Pay Via Razorpay
    </button>
</div>



@assets
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
@endassets

@push('script')
    <script>

        function getPaymentInstance() {
            let options = @js($configuration);
            options.modal = {
                ondismiss: function () {
                    window.location.replace(@js($failureUrl));
                }
            };
            return new Razorpay(options);
        }


        document.getElementById('rzp-button1').onclick = function (e) {

            let rzp1 = getPaymentInstance();
            rzp1.open();
            e.preventDefault();
        }

        window.addEventListener("load", (event) => {
            let isPending = @js($payable);
            if (isPending) {
                let rzp1 = getPaymentInstance();
                rzp1.open();
                e.preventDefault();
            }
        });


    </script>

@endpush


