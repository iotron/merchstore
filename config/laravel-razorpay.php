<?php




return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Razorpay Configuration
    |--------------------------------------------------------------------------
    |
    | This section contains the configuration for the payment model.
    |
    */

    'payment' => [
        'model' => [
            'class' => \App\Models\Payment\Payment::class,
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Provider Configuration
    |--------------------------------------------------------------------------
    |
    | This section contains the configuration for the payment provider.
    |
    | 'status' can be set to true to enable or false to disable the provider.
    | 'url' specifies the URL endpoint for the provider.
    | 'model' defines the model to be used for payment operations.
    |
    | If status is true, authentication configuration loads from model only.
    |
    */

    'payment-provider' => [
        'status' => true,
        'url' => \App\Services\Iotron\LaravelRazorpay\LaravelRazorpay::RAZORPAY,
        'model' => [
            'class' =>  \App\Models\Payment\PaymentProvider::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Razorpay Authentication
    |--------------------------------------------------------------------------
    |
    | This section contains the default Razorpay authentication keys.
    |
    | 'key' is the Razorpay key.
    | 'secret' is the Razorpay secret key.
    | 'webhook' is the Razorpay webhook secret (required for payment confirmation).
    |
    */

    'auth' => [
        'key' => env('RAZORPAY_KEY'),
        'secret' => env('RAZORPAY_SECRET'),
        'webhook' => env('RAZORPAY_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Refund Speed Configuration
    |--------------------------------------------------------------------------
    |
    | This option controls the speed of the refund processing.
    | Possible values are 'normal' or 'optimum' based on your application's requirement.
    |
    */

    'speed' => 'normal',

    /*
    |--------------------------------------------------------------------------
    | QR Code Configuration
    |--------------------------------------------------------------------------
    |
    | This section contains the configuration for QR codes.
    |
    | 'type' specifies the type of QR code to use (upi_qr or bharat_qr) Default: upi_qr.
    | 'usage' determines whether the QR code is single-use (dynamic) or multiple-use (static).
    | Note: Razorpay Tips - QR codes that support multiple payments cannot be closed.
    |
    */

    'qr' => [
        'type' => 'upi_qr',
        'usage' => 'single_use',
    ],

];
