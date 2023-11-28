@php
    $address = $getState();
@endphp

<div class="p-4 m-2 rounded-md @if(isset($textAlign) && !empty($textAlign)) text-{{$textAlign}}  @endif">
    <h2 class="text-xl font-bold">{{ $label }}</h2>
    <p><strong>Name:</strong> {{ $address['name'] }}</p>
    <p><strong>Type:</strong> {{ $address['type'] }}</p>
    <p><strong>Address:</strong> {{ $address['address_1'] }}</p>
    <p><strong>City:</strong> {{ $address['city'] }}, {{ $address['state'] }}</p>
    <p><strong>Postal Code:</strong> {{ $address['postal_code'] }}</p>
    <p><strong>Country:</strong> {{ $address['country_code'] }}</p>
</div>
