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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'openrouter' => [
        'key' => env('OPENROUTER_API_KEY'),
        'url' => env('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1/chat/completions'),
        'examiner_model' => env('OPENROUTER_EXAMINER_MODEL', env('OPENROUTER_MODEL', 'google/gemini-flash-1.5')),
        'chat_model' => env('OPENROUTER_CHAT_MODEL', env('OPENROUTER_MODEL', 'google/gemini-flash-1.5')),
        'model' => env('OPENROUTER_MODEL', 'google/gemini-flash-1.5'),
        'site_url' => env('OPENROUTER_SITE_URL', env('APP_URL')),
        'site_name' => env('OPENROUTER_SITE_NAME', env('APP_NAME')),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI', '/auth/facebook/callback'),
    ],

    'sepay' => [
        'merchant_id' => env('SEPAY_MERCHANT_ID'),
        'secret_key' => env('SEPAY_SECRET_KEY'),
        'environment' => env('SEPAY_ENVIRONMENT', 'sandbox'),
    ],

];
