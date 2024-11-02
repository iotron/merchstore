<?php

namespace App\Services\Iotron\LaravelRazorpay\Support\Traits;

use App\Services\Iotron\LaravelRazorpay\Support\Cast\PaymentModelTypeCast;

/**
 * Trait HasRazorpayOrderCreationDataFilter
 *
 * Provides methods to filter and format data from Razorpay order creation responses.
 */
trait HasRazorpayOrderCreationDataFilter
{
    /**
     * Filters and formats the data from a Razorpay API response.
     *
     * @param mixed $response The API response from Razorpay.
     * @param array $data The original data used to create the order.
     * @param PaymentModelTypeCast $type The type of the payment model.
     * @param array $notes Additional notes related to the response.
     *
     * @return array The filtered and formatted data.
     */
    protected function getFilterData($response, array $data,PaymentModelTypeCast $type, array $notes): array
    {
        $responseData = $response->toArray();

        // Check for errors in the response and set them in the provider
        if (isset($responseData['error'])) {
            $this->provider->setError($responseData['error']['description']);
        }

        return [
            'success' => $this->provider->getError() === null,
            'error' => $this->provider->getError(),
            'data' => [
                'provider_gen_id' => $responseData['id'], // Ensure consistency with API response
                'provider_transaction_id' => null,
                'provider_generated_sign' => null,
                'amount' => $responseData['amount'] ?? $responseData['payment_amount'] ?? $data['amount'] ?? 0,
                'callback_url' => $data['callback_url'] ?? '',
                'provider_gen_url' => $responseData['image_url'] ?? $responseData['short_url'] ?? null,
                'payment_provider_id' => $this->provider->getModel()?->id ?? null,
                'type' => $type,
                'details' => [
                    'provider' => $responseData,
                    'additional' => $notes,
                ],
            ],
            'provider' => $responseData,
            'response' => $response,
        ];

    }
}
