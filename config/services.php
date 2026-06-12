<?php

return [
    // SMS Service Configuration
    'sms' => [
        'provider' => env('SMS_PROVIDER', 'africastalking'),
        'africastalking' => [
            'username' => env('AFRICASTALKING_USERNAME'),
            'api_key' => env('AFRICASTALKING_API_KEY'),
            'from' => env('AFRICASTALKING_FROM'),
        ],
    ],

    // Payment Gateway Configuration
    'payment' => [
        'provider' => env('PAYMENT_PROVIDER', 'flutterwave'),
        'flutterwave' => [
            'public_key' => env('FLUTTERWAVE_PUBLIC_KEY'),
            'secret_key' => env('FLUTTERWAVE_SECRET_KEY'),
            'encryption_key' => env('FLUTTERWAVE_ENCRYPTION_KEY'),
        ],
    ],
];
