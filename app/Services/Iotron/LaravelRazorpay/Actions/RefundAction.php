<?php

namespace App\Services\Iotron\LaravelRazorpay\Actions;

use App\Services\Iotron\LaravelRazorpay\LaravelRazorpay;

/**
 * Class RefundAction
 *
 * Handles actions related to Razorpay refunds, including creating a refund, fetching a specific refund, and retrieving all refunds.
 */
class RefundAction
{
    /**
     * The instance of LaravelRazorpay service.
     *
     * @var LaravelRazorpay
     */
    protected LaravelRazorpay $provider;

    /**
     * RefundAction constructor.
     *
     * @param LaravelRazorpay $razorpay The instance of LaravelRazorpay service.
     */
    public function __construct(LaravelRazorpay $razorpay)
    {
        $this->provider = $razorpay;
    }

    /**
     * Create a new refund for a given payment.
     *
     * @param string $razorpay_payment_id The ID of the payment for which to issue a refund.
     * @param int $amount The amount to refund. This should be in the smallest currency unit (e.g., paise for INR).
     * @return array The response from the Razorpay API, including the refund details and any error messages.
     */
    public function create(string $razorpay_payment_id, int $amount): array
    {
        $response = null;
        $success = true;
        $error = null;

        try {
            $response = $this->provider->getApi()->payment->fetch($razorpay_payment_id)->refund([
                'amount' => $amount,
                'speed' => config('laravel-razorpay.speed'),
            ]);
        } catch (\Throwable $e) {
            $error = $e->getMessage();
            $success = false;
        }

        $responseData = !is_null($response) ? $response->toArray() : [];

        return [
            'success' => $success,
            'error' => $error,
            'data' => [
                'refund_id' => $responseData['id'] ?? null,
                'payment_id' => $responseData['payment_id'] ?? null,
                'status' => $responseData['status'] ?? null,
                'amount' => $responseData['amount'] ?? null,
                'details' => $responseData,
            ],
            'response' => $response,
        ];
    }

    /**
     * Fetch a specific refund by its identifier.
     *
     * @param string $id The identifier of the refund to fetch.
     * @return mixed The refund data returned by the Razorpay API.
     */
    public function fetch(string $id): mixed
    {
        return $this->provider->getApi()->refund->fetch($id);
    }

    /**
     * Retrieve all refunds based on the provided data.
     *
     * @param array $data Optional data to filter the results.
     * @return mixed The list of refunds returned by the Razorpay API.
     */
    public function all(array $data = []): mixed
    {
        return $this->provider->getApi()->refund->all($data);
    }
}
