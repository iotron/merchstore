<?php

namespace App\Services\Iotron\LaravelRazorpay\Actions;

use App\Services\Iotron\LaravelRazorpay\LaravelRazorpay;

/**
 * Class CustomerAction
 *
 * Handles actions related to Razorpay customers, including creation, fetching, and retrieving all customers.
 */
class CustomerAction
{
    /**
     * The instance of LaravelRazorpay service.
     *
     * @var LaravelRazorpay
     */
    protected LaravelRazorpay $provider;

    /**
     * CustomerAction constructor.
     *
     * @param LaravelRazorpay $razorpay The instance of LaravelRazorpay service.
     */
    public function __construct(LaravelRazorpay $razorpay)
    {
        $this->provider = $razorpay;
    }

    /**
     * Create a new Razorpay customer with the provided data.
     *
     * @param array $data The data for creating the customer.
     * @param array $notes Optional notes to include with the customer.
     * @return array The response from the Razorpay API as an array.
     */
    public function create(array $data, array $notes = []): array
    {
        $response = $this->provider->getApi()->customer->create($data);
        return [
            'success' => is_null($this->provider->getError()),
            'error' => $this->provider->getError(),
            'data' => [
                'details' => [
                    'provider' => $response->toArray(),
                    'additional' => $notes
                ]
            ],
            'response' => $response,
        ];
    }

    /**
     * Fetch a Razorpay customer by its identifier.
     *
     * @param string $id The identifier of the customer to fetch.
     * @return mixed The customer data returned by the Razorpay API.
     */
    public function fetch(string $id): mixed
    {
        return $this->provider->getApi()->customer->fetch($id);
    }

    /**
     * Retrieve all Razorpay customers based on the provided data.
     *
     * @param array $data Optional data to filter the customers.
     * @return mixed The list of customers returned by the Razorpay API.
     */
    public function all(array $data = []): mixed
    {
        return $this->provider->getApi()->customer->all($data);
    }
}
