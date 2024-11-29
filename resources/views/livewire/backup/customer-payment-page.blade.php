<div class="h-screen w-full bg-fuchsia-800  p-20">
    {{-- Be like water. --}}

    <div class="flex h-full justify-center items-center">

        <div class="grid gap-4">
            <span class="text-white text-3xl text-center">Order Details</span>
            {{--  Timer--}}
            <div class="text-center text-white">
                {{-- When Not Using Manual JS Formatter--}}
{{--                <h2>Time left: <span id="timeLeft">{{ gmdate('i:s', $timeLeft) }}</span></h2>--}}
                {{-- Manual JS Formatter--}}
                <h2>Time left: <span id="timeLeft"></span></h2>
            </div>
            {{--  Timer--}}
            <div class="relative overflow-x-auto rounded-3xl sm:rounded-lg shadow-lg overflow-x-hidden bg-white">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 ">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Particular
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Description
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Receipt
                        </th>
                        <td class="px-6 py-4 text-gray-700 font-semibold">
                            {{ $payment->receipt }}
                        </td>
                    </tr>
                    <tr class="border-b bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Order
                        </th>
                        <td class="px-6 py-4 text-gray-700 font-semibold">
                            {{ $payment->provider_gen_id }}
                        </td>
                    </tr>

                    <tr class="border-b bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Amount
                        </th>
                        <td class="px-6 py-4 text-gray-500 font-semibold">
                            {{ $payment->total->formatted() }}
                        </td>
                    </tr>

                    <tr class="border-b bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Event
                        </th>
                        <td class="px-6 py-4 text-gray-500 font-semibold">
                            {{ $payment->details['notes']['event_name'] }}
                        </td>
                    </tr>


                    <tr class="border-b bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Total Tickets
                        </th>
                        <td class="px-6 py-4 text-gray-500 font-semibold">
                            {{ $ticketQuantity }}
                        </td>
                    </tr>


                    @if(isset($payment->booking->id))
                    <tr class="border-b bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Booking ID
                        </th>
                        <td class="px-6 py-4 text-gray-700 font-semibold">
                            {{ $payment->booking->uuid }}
                        </td>
                    </tr>
                    @endif

                    <tr class="border-b bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Payment Status
                        </th>
                        <td class="px-6 py-4  font-semibold">
                            <span class="text-gray-700">{{ \App\Models\Customer\Payment::STATUS_OPTION[$payment->status] }}</span>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </div>



        @if($shouldRetry && $payment->status == \App\Models\Customer\Payment::PENDING && !$paymentExpire)
            <button
                class="px-5 py-2 rounded-2xl bg-white text-fuchsia-800 font-semibold hover:bg-gray-300 hover:text-purple-600"
                onclick="retryPayment()">
                Retry
            </button>
        @endif

            @if($paymentExpire)
                @if($payment->status == \App\Models\Customer\Payment::COMPLETED)
                    <a
                        class="px-5 py-2 rounded-2xl bg-fuchsia-600 text-white font-semibold text-center shadow-2xl"
                        href="{{config('app.client_url')}}">Back To Home</a>
                @else
                    <a
                        class="px-5 py-2 rounded-2xl bg-fuchsia-600 text-white font-semibold text-center shadow-2xl"
                        href="{{config('app.client_url')}}">Payment Expired</a>
                @endif

            @endif

        </div>
    </div>


</div>











@push('script')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>


        function initPayment()
        {
            let rConfig = @this.setup
            let options = {
                "key": rConfig['key'], // Enter the Key ID generated from the Dashboard
                "amount": rConfig['amount'], // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
                "currency": rConfig['currency'],
                "name": rConfig['name'],
                "description": rConfig['description'],
                "image": rConfig['image'],
                "order_id": rConfig['order_id'], //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
                "callback_url": rConfig['callback_url'],  // callback url booking
                "prefill": {
                    "name": rConfig['prefill']['name'],
                    "email": rConfig['prefill']['email'],
                    "contact": rConfig['prefill']['contact']
                },
                "notes": {
                    "address": "Razorpay Corporate Office"
                },
                "theme": {
                    "color": rConfig['theme']['color']
                },
                "modal": {
                    "ondismiss": function(){
                    @this.shouldRetry = true;
                        console.log( @this.shouldRetry);
                    }
                }
            };
            let rzp1 = new Razorpay(options);
            let status = @this.status


            let paymentExpireTime = @this.paymentExpiredAt

            if (status === "pending") {
                const countDownDate = new Date(paymentExpireTime).getTime();
                const now = new Date().getTime();
                // timeLimit in total of seconds
                let timeLimit = Math.floor((countDownDate - now) / 1000);
                if (timeLimit > 0) {
                    rzp1.open();
                    rzp1.on('handler', function (response){
                        localStorage.setItem('demo-invoice', response);
                    });
                }
            }
        }



        document.addEventListener('livewire:load', function () {


            initPayment();

            //TimeCountdown
            let countdownElement = document.getElementById('timeLeft');
            let timeLeft = @this.timeLeft; // Initial time in seconds

            let countdownInterval = setInterval(function () {
                countdownElement.innerText = formatTime(timeLeft);
                timeLeft--;

                if (timeLeft < 0) {
                    clearInterval(countdownInterval);
                    // Time is up, perform any actions you need
                    countdownElement.innerText = 'Expired!';
                }
            }, 1000); // Update the timer every second (1000 milliseconds)


            function formatTime(time)
            {
                let minutes = Math.floor(time / 60);
                let seconds = time % 60;
                return ('0' + minutes).slice(-2) + ':' + ('0' + seconds).slice(-2);
            }

        })





        function retryPayment()
        {
            let rConfig = @this.setup
            let options = {
                "key": rConfig['key'], // Enter the Key ID generated from the Dashboard
                "amount": rConfig['amount'], // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
                "currency": rConfig['currency'],
                "name": rConfig['name'],
                "description": rConfig['description'],
                "image": rConfig['image'],
                "order_id": rConfig['order_id'], //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
                "callback_url": rConfig['callback_url'],  // callback url booking
                "prefill": {
                    "name": rConfig['prefill']['name'],
                    "email": rConfig['prefill']['email'],
                    "contact": rConfig['prefill']['contact']
                },
                "notes": {
                    "address": "Razorpay Corporate Office"
                },
                "theme": {
                    "color": rConfig['theme']['color']
                },
                "modal": {
                    "ondismiss": function(){
                    @this.shouldRetry = true;
                        console.log( @this.shouldRetry);
                    }
                }
            };
            let rzp1 = new Razorpay(options);
            let status = @this.status


            let paymentExpireTime = @this.paymentExpiredAt

            if (status === "pending") {
                const countDownDate = new Date(paymentExpireTime).getTime();
                const now = new Date().getTime();
                // timeLimit in total of seconds
                let timeLimit = Math.floor((countDownDate - now) / 1000);
                if (timeLimit > 0) {
                    rzp1.open();
                    rzp1.on('handler', function (response){
                        localStorage.setItem('demo-invoice', response);
                    });
                }
            }
        }


    </script>


@endpush
