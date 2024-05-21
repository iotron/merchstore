<x-invoice.invoice-layout>


    {{--    {{ dd($booking) }}--}}
    {{--    {{ dd($booking->tickets) }}--}}

    <div id="details" class="clearfix">
        <div id="client">
            <div class="to">Order To:</div>
            <h2 class="name">{{ $order->customer->name}}</h2>
            <div class="address">{{ $order->customer->contact }}</div>
            <div class="email"><a href="mailto:{{ $order->customer->email }}">{{ $order->customer->email }}</a></div>
        </div>
        @if(isset($qr))
            <div id="qrcode">

                <img src="{{ $qr }}">

            </div>
        @endif
        <div id="invoice">
            <div style="padding-right: 10px;">
                <h2>{{$title}}</h2>
                <span class="name">Order ID</span>
                <h1 style="font-weight: bolder;">{{ $order->uuid }}</h1>
            </div>


            {{--            <h1>Booking ID </h1>--}}
            {{--            <div class="date">Date of Invoice: {{ \Carbon\Carbon::parse($booking->created_at)->format("d/m/Y") }}</div>--}}

        </div>
    </div>


    <div><h3 style="color: #560269">Products </h3></div>


    <table border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th class="no">#</th>
            <th class="desc">DESCRIPTION</th>
            <th class="unit">UNIT PRICE</th>
            <th class="center">TAX</th>
            <th class="qty">QUANTITY</th>
            <th class="center">SUBTOTAL</th>
            <th class="center">DISCOUNT</th>
            <th class="center">TOTAL TAX</th>
            <th class="total">TOTAL</th>
        </tr>
        </thead>


        <tbody>

        @foreach ($order->orderProducts as $key => $orderProduct)

            {{--            {{ dd($orderProduct) }}--}}
            <tr>
                <td class="no"> {{$key +1}}</td>
                <td class="desc">

                    <h3>{{ ucwords($orderProduct->product->name) }} </h3>
                </td>
                <td class="unit">{{ $orderProduct->product->price->formatted() }}</td>

                <td class="center">{{ $orderProduct->tax->formatted() }}</td>

                <td class="qty">{{ $orderProduct->quantity }}</td>
                <td class="total">{{ $orderProduct->product->price->multiplyOnce($orderProduct->quantity)->formatted()}}</td>

                {{--                <td class="center">--}}
                {{--                    @if( $orderProduct->amount instanceof \App\Helpers\Money\Money)--}}
                {{--                        {{$orderProduct->amount->formatted()}}--}}
                {{--                    @else--}}
                {{--                        {{\App\Helpers\Money\Money::format($orderProduct->amount)}}--}}
                {{--                    @endif--}}
                {{--                </td>--}}

                <td class="center">
                    @if($orderProduct->discount instanceof \App\Services\Iotron\Money\Money)
                        {{$orderProduct->discount->formatted()}}
                    @else
                        {{\App\Services\Iotron\Money\Money::format($orderProduct->discount)}}
                    @endif
                </td>

                <td class="center">
                    @if($orderProduct->tax instanceof \App\Services\Iotron\Money\Money)
                        {{$orderProduct->tax->formatted()}}
                    @else
                        {{\App\Services\Iotron\Money\Money::format($orderProduct->tax)}}
                    @endif
                </td>


                <td class="total">
                    @if($orderProduct->total instanceof \App\Services\Iotron\Money\Money)
                        {{$orderProduct->total->formatted()}}
                    @else
                        {{\App\Services\Iotron\Money\Money::format($orderProduct->total)}}
                    @endif
                </td>


            </tr>
        @endforeach
        </tbody>


        <tfoot>
        <tr>
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="2">SUBTOTAL</td>
            <td>{{ $order->subtotal->formatted() }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="2">DISCOUNT</td>
            <td>{{ $order->discount->formatted()}}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="2">TOTAL TAX</td>
            <td>{{ $order->tax->formatted()}}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="2">GRAND TOTAL</td>
            <td>{{ $order->total->formatted()}}</td>
        </tr>
        </tfoot>


    </table>


    <div style="position: relative; margin-top: 10px;">


        <div id="notices">
            {{--            <div><b><u>EVENT DETAILS:</u></b></div>--}}
            {{--            <span><b>Name :</b> {{ ucwords($booking->event->name) }}</span> <br />--}}
            {{--            <span style="display: flex;">--}}
            {{--                <span>--}}
            {{--                    @if($booking->event->type != \App\Models\Events\Events::OUTDOOR) <b>Live On:</b>  @else <b>Start On:</b>  @endif--}}


            {{--                    {{ \Carbon\Carbon::parse($booking->event->start_date)->format("d/m/Y").' - '.$booking->event->start_time }}</span>--}}
            {{--                &nbsp; - &nbsp;--}}
            {{--                <span><b>Ends On:</b> {{ \Carbon\Carbon::parse($booking->event->end_date)->format("d/m/Y").' - '.$booking->event->end_time }}</span>--}}
            {{--            </span>--}}

            {{--            @if($booking->event->type != \App\Models\Events\Events::OUTDOOR)--}}
            {{--                <strong>Location :-</strong>  <br />--}}
            {{--                <span> View Here : {{$booking->event->streaming_link}}</span> <br />--}}
            {{--                <span> Platform : {{$booking->event->streaming_platform}}</span> <br />--}}
            {{--            @else--}}
            {{--                <strong>Location :-</strong>  <br />--}}
            {{--                <span><strong>Address :</strong> {{$booking->event->location}}</span>--}}
            {{--                <span style="display: flex;">--}}
            {{--                    <span> <strong>City :</strong> {{$booking->event->city->name}} </span>--}}
            {{--                    &nbsp; - &nbsp;--}}
            {{--                    <span> <strong>Venue :</strong> {{$booking->event->location_code}}</span>--}}
            {{--                </span>--}}
            {{--            @endif--}}

        </div>


        <div id="thanks" style="position: absolute; top: 10px; right: 10px;">Thank you!</div>


    </div>


</x-invoice.invoice-layout>
