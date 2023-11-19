<div class="w-full h-full bg-fuchsia-900 overflow-hidden px-3 py-4">
    <h1 class="text-center text-3xl md:text-5xl font-medium underline py-2">Order Details</h1>
    <h4 class="text-center text-md lg:text-lg">Remaining: <span id="timeLeft"></span></h4>
        <div class="w-full h-full flex justify-center py-10 relative">

            <div class=" h-fit w-96 bg-white text-purple-900 px-2 pt-3 pb-1 rounded-2xl shadow-black shadow-lg">

                {{-- Order Details--}}
                <table class="w-full py-4">

                    <thred class="w-full">
                        <tr class="w-full  border-b-2 border-black">
                            <th class="" scope="col">
                                Particular
                            </th>

                            <th class="" scope="col">
                                Description
                            </th>
                        </tr>
                    </thred>


                    <tbody class="w-full py-5">

                    {{-- Receipt --}}
                    <tr scope="row" class="w-full py-2 md:py-4">
                        <th class="w-1/2 text-center ">Receipt</th>
                        <td class="w-1/2 text-center  font-medium">{{ $payment->receipt }}</td>
                    </tr>
                    {{-- Receipt ./--}}

                    {{-- Provider Gen ID --}}
                    <tr scope="row" class="w-full py-2 md:py-4">
                        <th class="w-1/2 text-center ">Order</th>
                        <td class="w-1/2 text-center  font-medium">{{ $payment->provider_gen_id }}</td>
                    </tr>
                    {{-- Provider Gen ID --}}

                    {{-- Order Total --}}
                    <tr scope="row" class="w-full py-2 md:py-4">
                        <th class="w-1/2 text-center ">Amount</th>
                        <td class="w-1/2 text-center  font-medium">{{ $payment->total->formatted() }}</td>
                    </tr>
                    {{-- Order Total ./--}}

                    {{-- Quantity --}}
                    <tr scope="row" class="w-full py-2 md:py-4">
                        <th class="w-1/2 text-center ">Total Products</th>
                        <td class="w-1/2 text-center  font-medium">{{ $quantity }}</td>
                    </tr>
                    {{-- Quantity ./--}}

                    {{-- Event Name --}}
                    {{-- <tr scope="row" class="w-full py-2 md:py-4">
                        <th class="w-1/2 text-center ">Event</th>
                        <td class="w-1/2 text-center  font-medium">{{ $event->name }}</td>
                    </tr> --}}
                    {{-- Event Name ./--}}

                    </tbody>


                </table>
                {{-- Order Details ./--}}



                {{-- Actions--}}
                <div class="w-full h-full py-2">

                    @if($paymentExpire)
                        <button wire:click="returnClient" class="w-full bg-red-700 text-white text-lg md:text-xl py-1 rounded-xl shadow-black shadow-lg">Go Back</button>
                    @else
                        <livewire:pay-button :payment="$payment"/>
                    @endif



                </div>
                {{-- Actions ./--}}


            </div>





        </div>


</div>





@push('script')
    <script>
        function formatTime(time) {
            let minutes = Math.floor(time / 60);
            let seconds = time % 60;
            return ('0' + minutes).slice(-2) + ':' + ('0' + seconds).slice(-2);
        }


        // Countdown
        let countdownElement = document.getElementById('timeLeft');
        let timeLeft = @js($timeout);
        if(timeLeft == 0){
            countdownElement.innerText = 'Expired!';
        }
        if(timeLeft > 0)
        {
            let countdownInterval = setInterval(function () {
                countdownElement.innerText = formatTime(timeLeft);
                timeLeft--;
                if (timeLeft < 0) {
                    clearInterval(countdownInterval);
                    countdownElement.innerText = 'Expired!';
                    location.reload();
                }
            }, 1000);
        }



    </script>
@endpush
