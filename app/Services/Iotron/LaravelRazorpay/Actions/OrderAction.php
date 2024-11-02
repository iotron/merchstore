<?php

namespace App\Services\Iotron\LaravelRazorpay\Actions;

use App\Services\Iotron\LaravelRazorpay\LaravelRazorpay;
use App\Services\Iotron\LaravelRazorpay\Support\Cast\PaymentModelTypeCast;
use App\Services\Iotron\LaravelRazorpay\Support\Traits\HasRazorpayOrderCreationDataFilter;

/**
 * Class OrderAction
 *
 * Handles actions related to Razorpay orders, including creation, fetching, and retrieving all orders.
 */
class OrderAction
{
    use HasRazorpayOrderCreationDataFilter;

    /**
     * The instance of LaravelRazorpay service.
     *
     * @var LaravelRazorpay
     */
    protected LaravelRazorpay $provider;

    /**
     * OrderAction constructor.
     *
     * @param LaravelRazorpay $razorpay
     */
    public function __construct(LaravelRazorpay $razorpay)
    {
        $this->provider = $razorpay;
    }

    /**
     * Create a new Razorpay order.
     *
     * @param array $data The data for creating the order.
     * @param array $notes Optional notes to include with the order.
     * @return array The response from the Razorpay API, filtered by the specified notes and data.
     */
    public function create(array $data, array $notes = []): array
    {
        // Create the order using Razorpay API
        $response = $this->provider->getApi()->order->create($data);

        // Filter and return the response data
        return $this->getFilterData($response, $data, PaymentModelTypeCast::STANDARD, $notes);
    }

    /**
     * Fetch a Razorpay order by its identifier.
     *
     * @param string $id The identifier of the order to fetch.
     * @return mixed The order data returned by the Razorpay API.
     */
    public function fetch(string $id): mixed
    {
        return $this->provider->getApi()->order->fetch($id);
    }

    /**
     * Retrieve all Razorpay orders based on the provided data.
     *
     * @param array $data Optional data to filter the orders.
     * @return mixed The list of orders returned by the Razorpay API.
     */
    public function all(array $data = []): mixed
    {
        return $this->provider->getApi()->order->all($data);
    }
}
