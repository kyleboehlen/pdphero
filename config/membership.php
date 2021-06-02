<?php
return [
    'trial_length' => env('MEMBERSHIP_TRIAL_LENGTH_IN_DAYS', 60),
    'basic' => [
        'slug' => 'basic',
        'price' => env('BASIC_MEMEBERSHIP_PRICE', 5.99),
        'stripe_price_id' => env('BASIC_STRIPE_PRICE_ID'),
    ],
    'black_label' => [
        'slug' => 'black-label',
        'price' => env('BLACK_LABEL_MEMEBERSHIP_PRICE', 10.99),
        'stripe_price_id' => env('BLACK_LABEL_STRIPE_PRICE_ID'),
    ],
];