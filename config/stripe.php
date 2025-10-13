<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Stripe payment processing including subscription tiers,
    | credit packages, and pricing information for the background removal service.
    |
    */

    'subscription_tiers' => [
        [
            'id' => 'starter',
            'name' => 'Starter',
            'description' => 'Perfect for trying out',
            'price' => 3,
            'period' => 'month',
            'credits_included' => 50,
            'features' => [
                '50 background removals per month',
                'High-quality AI processing',
                'Email support',
                'Download in multiple formats',
            ],
            'stripe_price_id' => env('STRIPE_STARTER_PRICE_ID', 'price_starter_monthly'),
        ],
        [
            'id' => 'basic',
            'name' => 'Basic',
            'description' => 'Great for light usage',
            'price' => 7,
            'period' => 'month',
            'credits_included' => 150,
            'features' => [
                '150 background removals per month',
                'High-quality AI processing',
                'Email support',
                'Priority processing',
                'Download in multiple formats',
            ],
            'stripe_price_id' => env('STRIPE_BASIC_PRICE_ID', 'price_basic_monthly'),
        ],
        [
            'id' => 'professional',
            'name' => 'Professional',
            'description' => 'Best value for regular users',
            'price' => 15,
            'period' => 'month',
            'credits_included' => 400,
            'features' => [
                '400 background removals per month',
                'High-quality AI processing',
                'Priority support',
                'Bulk processing',
                'API access',
                'Download in multiple formats',
            ],
            'stripe_price_id' => env('STRIPE_PROFESSIONAL_PRICE_ID', 'price_professional_monthly'),
            'popular' => true,
        ],
        [
            'id' => 'business',
            'name' => 'Business',
            'description' => 'For growing teams',
            'price' => 35,
            'period' => 'month',
            'credits_included' => 1000,
            'features' => [
                '1000 background removals per month',
                'High-quality AI processing',
                'Priority support',
                'Bulk processing',
                'API access',
                'Team management',
                'Download in multiple formats',
            ],
            'stripe_price_id' => env('STRIPE_BUSINESS_PRICE_ID', 'price_business_monthly'),
        ],
    ],

    'credit_packages' => [
        [
            'id' => 'credits_10',
            'name' => '10 Credits',
            'description' => 'Quick try',
            'credits' => 10,
            'price' => 2.00,
            'price_per_credit' => 0.20,
            'savings' => null,
            'stripe_price_id' => env('STRIPE_CREDITS_10_PRICE_ID', 'price_credits_10'),
        ],
        [
            'id' => 'credits_25',
            'name' => '25 Credits',
            'description' => 'Perfect for testing',
            'credits' => 25,
            'price' => 4.00,
            'price_per_credit' => 0.16,
            'savings' => 'Save $1',
            'stripe_price_id' => env('STRIPE_CREDITS_25_PRICE_ID', 'price_credits_25'),
        ],
        [
            'id' => 'credits_50',
            'name' => '50 Credits',
            'description' => 'Great for projects',
            'credits' => 50,
            'price' => 7.00,
            'price_per_credit' => 0.14,
            'savings' => 'Save $3',
            'stripe_price_id' => env('STRIPE_CREDITS_50_PRICE_ID', 'price_credits_50'),
            'popular' => true,
        ],
        [
            'id' => 'credits_100',
            'name' => '100 Credits',
            'description' => 'Best value package',
            'credits' => 100,
            'price' => 12.00,
            'price_per_credit' => 0.12,
            'savings' => 'Save $8',
            'stripe_price_id' => env('STRIPE_CREDITS_100_PRICE_ID', 'price_credits_100'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Settings
    |--------------------------------------------------------------------------
    |
    | Additional Stripe configuration settings for the application.
    |
    */

    'webhook_tolerance' => 300,

    'success_url' => env('STRIPE_SUCCESS_URL'),
    'cancel_url' => env('STRIPE_CANCEL_URL'),

];
