<?php

namespace App\Services\Iotron\LaravelRazorpay\Actions;

use App\Services\Iotron\LaravelRazorpay\LaravelRazorpay;
use App\Services\Iotron\LaravelRazorpay\Support\Cast\PaymentModelTypeCast;
use App\Services\Iotron\LaravelRazorpay\Support\Traits\HasRazorpayOrderCreationDataFilter;

/**
 * Class QRCodeAction
 *
 * Handles actions related to Razorpay QR codes, including creation, fetching, retrieving all QR codes, and closing a QR code.
 */
class QRCodeAction
{
    use HasRazorpayOrderCreationDataFilter;

    /**
     * The instance of LaravelRazorpay service.
     *
     * @var LaravelRazorpay
     */
    protected LaravelRazorpay $provider;

    /**
     * QRCodeAction constructor.
     *
     * @param LaravelRazorpay $razorpay The instance of LaravelRazorpay service.
     */
    public function __construct(LaravelRazorpay $razorpay)
    {
        $this->provider = $razorpay;
    }

    /**
     * Fetch a Razorpay QR code by its identifier.
     *
     * @param int|string $id The identifier of the QR code to fetch.
     * @return mixed The QR code data returned by the Razorpay API.
     */
    public function fetch(int|string $id): mixed
    {
        return $this->provider->getApi()->qrCode->fetch($id);
    }

    /**
     * Retrieve all Razorpay QR codes based on the provided data.
     *
     * @param array $data Optional data to filter the results.
     * @return mixed The list of QR codes returned by the Razorpay API.
     */
    public function all(array $data = []): mixed
    {
        return $this->provider->getApi()->qrCode->all($data);
    }

    /**
     * Create a new Razorpay QR code with the provided data.
     *
     * @param array $data The data for creating the QR code.
     * @param array $notes Optional notes to include with the QR code.
     * @return array The response from the Razorpay API as an array.
     */
    public function create(array $data, array $notes = []): array
    {
        $response = $this->provider->getApi()->qrCode->create(array_merge($data, [
            'type' => config('laravel-razorpay.qr.type','upi_qr'),
            'usage' => config('laravel-razorpay.qr.usage'),
            'fixed_amount' => true,
        ]));
        return $this->getFilterData($response, $data, PaymentModelTypeCast::QR, $notes);
    }

    /**
     * Close a Razorpay QR code by its identifier.
     *
     * @param string $id The identifier of the QR code to close.
     * @return array The result of the close operation, including status and response data.
     */
    public function close(string $id): array
    {
        $response = $this->fetch($id)->close();
        $responseData = $response->toArray();
        return [
            'status' => $responseData['status'],
            'data' => $responseData,
            'response' => $response,
        ];
    }
}
