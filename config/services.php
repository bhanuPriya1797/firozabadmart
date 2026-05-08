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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA v3 Configuration
    |--------------------------------------------------------------------------
    |
    | reCAPTCHA v3 helps protect your website from spam and abuse without
    | creating friction for users. It returns a score for each request
    | without requiring any user interaction.
    |
    | Setup Instructions:
    | 1. Visit https://www.google.com/recaptcha/admin
    | 2. Create a new site with reCAPTCHA v3
    | 3. Add your domain(s) (use 'localhost' for development)
    | 4. Copy the Site Key and Secret Key to your .env file:
    |    RECAPTCHA_SITE_KEY=your_site_key_here
    |    RECAPTCHA_SECRET_KEY=your_secret_key_here
    |
    | Usage:
    | - site_key: Used in frontend JavaScript (public, safe to expose)
    | - secret_key: Used for server-side verification (private, keep secure)
    |
    | Security Notes:
    | - Never expose the secret_key in frontend code
    | - Always verify tokens on the server side
    | - Tokens expire in 2 minutes
    | - Implement appropriate score thresholds (typically 0.5)
    |
    */
    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),     // Public key for frontend integration
        'secret_key' => env('RECAPTCHA_SECRET_KEY'), // Private key for server-side verification
    ],

];
