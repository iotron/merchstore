<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],


    /*
     * Custom Services
     */

    'defaults' => [
        'country' => 'IN',
        'currency' => 'INR',
        'locale' => 'en',
        'max_gst_free_amount' => 50000,
        'commission_percent' => '25',
        'payout' => [
            'tax' => 0,
            'tcs' => 0,
            'tds' => 0
        ],
        'order_cleanup_time_limit' => env('ORDER_EXPIRED_TIME_LIMIT_IN_MINUTE', 120),
    ],


    // Payment Providers

    'razorpay' => [
        'api_key' => env('RAZORPAY_KEY'),
        'api_secret'=> env('RAZORPAY_SECRET'),
        'provider'=> 'razorpay',
        'webhook_secret'=> env('RAZORPAY_WEBHOOK_SECRET'),

        'api_x_key' => env('RAZORPAY_X_KEY'),
        'api_x_secret'=> env('RAZORPAY_X_SECRET'),

        'payout' => [
            'account_no' => 2323230020266990,
            'mode' => 'NEFT',

        ]
    ],
    'stripe' => [
        'sk_api_key' => env('STRIPE_SK_KEY'),
        'pk_api_key' => env('STRIPE_PK_KEY')
    ],


    // Shipping Providers

    'shiprocket' => [
        'key' => env('SHIPROCKET_EMAIL','your key'),
        'secret' => env('SHIPROCKET_PASSWORD','your secret'),
//        'default' => false,
        'webhook' => env('SHIPROCKET_WEBHOOK',''),
    ],



    'custom-shipping' => [

    ],

    'custom_payment' => [
        'prefix' => 'cod_',
    ],






];
