<?php

return [

    'providers' => [

        // -------------------------------------
        // ----- Razorpay Payment Provider -----
        // -------------------------------------
        'razorpay' => [
            'speed' => 'normal', // options: normal, optimum
        ],

        // -------------------------------------
        // ----- Stripe Payment Provider -------
        // -------------------------------------
        'stripe' => [
            'take_transaction_fee' => false, // set application fee on transaction
            'fee_amount' => 250, // $2.50 fee
            'subscription' => false, // subscription status
            'terms' => true,  // display application terms&condition url
            'mode' => 'checkout', // mode options: intent , checkout
            // Mode Details
            'mode_data' => [
                // Custom Process Checkout
                'intent' => [
                    'type' => 'card', // options: card, wallet
                    'wallet' => [
                        'type' => 'apple_pay', // Replace with the appropriate wallet type (e.g., 'google_pay').
                    ],
                ],
                // Stripe Hosted Checkout
                'checkout' => [

                ],
            ],

        ],

        // -------------------------------------
        // ----- Other Payment Provider --------
        // -------------------------------------
    ],

];
