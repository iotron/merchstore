@props([
    'rec' => $getRecord(),
])


<div class="flex">
    <div class="flex-1">
        <div class="p-4 m-2 rounded-md">
            <h2 class="text-xl font-bold">Billing Address</h2>
            <p><strong>Name:</strong> {{ $rec->billingAddress->name }}</p>
            <p><strong>Type:</strong> {{ $rec->billingAddress->type }}</p>
            <p><strong>Address:</strong> {{ $rec->billingAddress->address_1 }}</p>
            <p><strong>City:</strong> {{ $rec->billingAddress->city }}, {{ $rec->billingAddress->state }}</p>
            <p><strong>Postal Code:</strong> {{ $rec->billingAddress->postal_code }}</p>
            <p><strong>Country:</strong> {{ $rec->billingAddress->country_code }}</p>
        </div>
    </div>


    <div class="flex-1 ml-auto">
        <div class=" p-4 m-2 rounded-md text-right">
            <h2 class="text-xl font-bold">Shipping Address</h2>
            <p><strong>Name:</strong> {{ $rec->shippingAddress->name }}</p>
            <p><strong>Type:</strong> {{ $rec->shippingAddress->type }}</p>
            <p><strong>Address:</strong> {{ $rec->shippingAddress->address_1 }}</p>
            <p><strong>City:</strong> {{ $rec->shippingAddress->city }}, {{ $rec->shippingAddress->state }}</p>
            <p><strong>Postal Code:</strong> {{ $rec->shippingAddress->postal_code }}</p>
            <p><strong>Country:</strong> {{ $rec->shippingAddress->country_code }}</p>
        </div>
    </div>
</div>
