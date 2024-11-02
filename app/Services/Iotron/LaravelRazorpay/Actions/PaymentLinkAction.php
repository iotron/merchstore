<?php

namespace App\Services\Iotron\LaravelRazorpay\Actions;

use App\Services\Iotron\LaravelRazorpay\LaravelRazorpay;
use App\Services\Iotron\LaravelRazorpay\Support\Cast\PaymentModelTypeCast;
use App\Services\Iotron\LaravelRazorpay\Support\Traits\HasRazorpayOrderCreationDataFilter;

/**
 * Class PaymentLinkAction
 *
 * Handles actions related to Razorpay payment links, including creation, fetching, and retrieving all payment links.
 */
class PaymentLinkAction
{
    use HasRazorpayOrderCreationDataFilter;

    /**
     * The instance of LaravelRazorpay service.
     *
     * @var LaravelRazorpay
     */
    protected LaravelRazorpay $provider;

    /**
     * PaymentLinkAction constructor.
     *
     * @param LaravelRazorpay $razorpay The instance of LaravelRazorpay service.
     */
    public function __construct(LaravelRazorpay $razorpay)
    {
        $this->provider = $razorpay;
    }

    /**
     * Fetch a Razorpay payment link by its identifier.
     *
     * @param int|string $id The identifier of the payment link to fetch.
     * @return mixed The payment link data returned by the Razorpay API.
     */
    public function fetch(int|string $id): mixed
    {
        return $this->provider->getApi()->paymentLink->fetch($id);
    }

    /**
     * Retrieve all Razorpay payment links.
     *
     * @return mixed The list of payment links returned by the Razorpay API.
     */
    public function all(): mixed
    {
        return $this->provider->getApi()->paymentLink->all();
    }

    /**
     * Create a new Razorpay payment link with the provided data.
     *
     * @param array $data The data for creating the payment link.
     * @param array $notes Optional notes to include with the payment link.
     * @return array The response from the Razorpay API as an array.
     */
    public function create(array $data, array $notes = []): array
    {
        $response = $this->provider->getApi()->paymentLink->create($data);
        return $this->getFilterData($response, $data, PaymentModelTypeCast::LINK, $notes);
    }
}
