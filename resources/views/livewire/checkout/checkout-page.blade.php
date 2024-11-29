<div>
    <div id="spinner" class="flex flex-col justify-center items-center h-screen">
        <div class="animate-spin rounded-full h-32 w-32 border-t-2 border-b-2 border-purple-500"></div>

        <h4 class="text-center text-md my-2">Remaining: <span id="timeLeft"></span></h4>
        <div class="mb-2 text-center">
            <h2 class="text-lg font-semibold ">Please wait, checkout initializing...</h2>
            <p class="">Do not close the page, refresh the page, or hit the back button.</p>
        </div>


        @if($paymentExpire)
            <button wire:click="returnBack" class="w-96 bg-red-700 text-white text-lg md:text-xl py-1 rounded-lg shadow-black shadow-lg flex justify-center">
                {{ svg('heroicon-o-arrow-left-start-on-rectangle','w-6 h-6') }}
                Go Back</button>
        @else
            @if($payBtn == config('laravel-razorpay.payment-provider.url'))
                <livewire:checkout.btn.razorpay :payment="$payment"/>
            @elseif($payBtn == 'stripe')
                <livewire:checkout.btn.stripe :payment="$payment"/>
            @else
                Contact Your Booking Manager
            @endif
        @endif


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
